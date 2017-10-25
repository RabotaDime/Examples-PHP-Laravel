<?php

namespace App\MyCode;

use DateTime;

use Illuminate\Support\Facades\Storage;



class CSVTrackingRecord
{
	public /* @var float	*/ $Speed;
	public /* @var float	*/ $CoordLong;
	public /* @var float	*/ $CoordFlat;
	public /* @var DateTime	*/ $DateTime;

	public const EmptyDateTimeString = '0000-00-00 00:00:00';



	public function SetDateFromString (string $aT)
	{
		$ValidFormats = ['Y-m-d H:i:s'];

		foreach ($ValidFormats as $DateFormat)
		{
			$D = DateTime::createFromFormat($DateFormat, $aT);
			if ($D !== false)
			{
				$this->DateTime = $D;
				return true;
			}
		}

		return false;
	}



	public function AssignFromCSVArray (array $aCSVArray)
	{
		$Count = count($aCSVArray);

		///  Извлекаем данные и устанавливаем значения по умолчанию, 
		///  в случае ошибочных/нулевых данных. 
		$this->DateTime		= null;
		$this->Speed		= ($Count >= 2) ? floatval($aCSVArray[1]) : 0;
		$this->CoordLong	= ($Count >= 3) ? floatval($aCSVArray[2]) : 0;
		$this->CoordFlat	= ($Count >= 4) ? floatval($aCSVArray[3]) : 0;

		///  Пытаемся распознать и установить дату. 
		$this->SetDateFromString( ($Count >= 1) ? $aCSVArray[0] : self::EmptyDateTimeString );

/*
		Временная отладка
		echo '<pre>';
		var_dump($aCSVArray);
		echo '</pre>';
		echo '<pre>';
		var_dump($this);
		echo '</pre>';
//		exit(0);
*/

		return ($Count > 0);
	}
}



interface ICSVTrackingConsumer
{
	public function SetCapacity (int $aSize);
	public function PushRecord (CSVTrackingRecord $aR);
	public function GetCount ();
}



class CSVTrackingJobFile
{
	///   Максимальный размер текстовой записи в символах. 
	public const MaximumRecordLength = 1000;

	///   Разделитель данных в текстовой записи (один символ). 
	public const RecordDelimiter = ';';



	public const InvalidCountResult			= -1;
	public const InvalidStreamResult		= -10;
	public const InvalidConsumerObject		= -20;



	public static function IsValidStream ($aFile)
	{
		///  Пытаемся узнать тип ресурса, свзяанного с этим файл-указателем. 
		$ResourceType = get_resource_type($aFile) or
			die("CSVJobFile :: Unable to validate stream.");

		$ValidTypes = ['file', 'stream'];
		$ValidFileHandle = false;

		///  Сверяем допустимые типы потоков этого файл-указателя. 
		foreach ($ValidTypes as $ValidType)
			if ($ResourceType == $ValidType)
			{
				$ValidFileHandle = true;
				break;
			}

		if
		(
			///  Если нам удается получить данные о файл-указателе. 
			( ($StatusResult = fstat($aFile)) !== false ) &&
			///  И если количество связок у этого файл-указателя больше нуля. 
			( $StatusResult['nlink'] > 0 )
		)
		{
			return $ValidFileHandle;
		}
		else
		{
			return false;
		}
	}



	public static function ProcessStream ($aFileStream, ICSVTrackingConsumer $aConsumer)
	{
		if (! self::IsValidStream($aFileStream))
		{
			///  Если данный поток не подходит по своему типу, либо является ошибочным. 
			return self::InvalidStreamResult;
		}

		if
		(
			///  Если указан недопустимый объект
			empty($aConsumer) ||
			///  Или объект не относится к нужному типу (в PHP 7 эта проверка не нужна)
			( !($aConsumer instanceof ICSVTrackingConsumer) )
		)
		{
			return self::InvalidConsumerObject;
		}



		$Record = new CSVTrackingRecord();
		$RecordsCount = 0;

		while
		(
			///  Считываем каждую запись из файла 
			($DataRecord = fgetcsv
			(
				$aFileStream,
				self::MaximumRecordLength,
				self::RecordDelimiter
			))
			///  До тех пор, пока функция не вернет отрицательный результат 
			///  (из-за достижения конца файла или иной ошибки). 
			!== false
		)
		{
			///  Если запись удалось обработать. 
			if ($Record->AssignFromCSVArray($DataRecord))
			{
				///  Передаем данные в Потребитель. 
				$aConsumer->PushRecord($Record);
				///  Увеличиваем счетчик извлеченных записей. 
				$RecordsCount++;
			}
		}

		unset($Record);

		return $RecordsCount;
	}
}



class CSVTrackingJobHelper
{
	public static function ProcessRequest ($aR, $aFileID, ICSVTrackingConsumer $aConsumer)
	{
		$RecordsCount = CSVTrackingJobFile::InvalidCountResult;

		if ($aR->hasFile($aFileID))
		{
			$RequestInputFile = $aR->file($aFileID);

			if (($InputStream = fopen($RequestInputFile, 'r+')) !== false)
			{
				$RecordsCount = CSVTrackingJobFile::ProcessStream($InputStream, $aConsumer);
			}
		}

		return $RecordsCount;
	}



	///  Функция для сохранения входящего файла для последующей обработки. 
	public static function StoreRequestFile ($aR, $aFileID)
	{
		if ($aR->hasFile($aFileID))
		{
			$RequestInputFile = $aR->file($aFileID);

			///  Формируем временное имя файла. 
			$TempFileName = uniqid('queued-job-', true) . '.csv';

			///  Идентификатор виртуального хранилища. 
			$StorageDisk = 'local';

			///   Сохраняю входящий файл с помощью записи входящего потока. 
			///   Используется стандартный класс Laravel "Storage". 
			$TempLocation = '/tracking-input-jobs/' . $TempFileName;
			$VirtualStorage = Storage::disk($StorageDisk);
			$VirtualStorage->put($TempLocation, fopen($RequestInputFile, 'r+'));

			return $TempFileName;
		}

		return '';
	}
}



class DebugCSVConsumer implements ICSVTrackingConsumer
{
	private $Counter = 0;

	public function SetCapacity (int $aSize)
	{
	}

	public function PushRecord (CSVTrackingRecord $aR)
	{
		if ($aR->DateTime !== null)
			$this->Counter++;
	}

	public function GetCount ()
	{
		return $this->Counter;
	}
}



