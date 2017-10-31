<?php

namespace App\My;

use DateTime;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

use App\My\WaypointsAPI\Data\VehiclesWaypoint as WaypointData;



class DBSpatialHelper
{
	///   Помогает сгенерировать часть чистого SQL запроса для Spatial-точки.                      
	///____________________________________________________________________________________________
	///   
	///   Пример SQL Spatial точки для московского кремля: 
	///      GeomFromText('POINT(37.617654 55.751234)') 
	///   
	///   SRID (SpatialID) это идентификатор для указания систем исчисления географических данных. 
	///   В данном случае, я использую один из стандартных для примера, так как PHPMyAdmin 
	///   не любит нулевое значение, а с указанным индетификтором показывает точку на карте 
	///   в своем интерфейсе через OpenStreetMap (www.openstreetmap.org) 
	///   
	public static function MakePointValue (float $XLng, float $YLat, $SpatialID = 4326)
	{
		return sprintf("ST_GeomFromText('POINT(%.6f %.6f)',%u)", $XLng, $YLat, $SpatialID);
	}

	public static function MakeXColumn (string $ColumnID)
	{
		return "X('" . $ColumnID . ")";
	}

	public static function MakeYColumn (string $ColumnID)
	{
		return "Y('" . $ColumnID . ")";
	}

	public static function MakeXColumnAs (string $ColumnID, string $AsID)
	{
		return "X('" . $ColumnID . ") as " . $AsID;
	}

	public static function MakeYColumnAs (string $ColumnID, string $AsID)
	{
		return "Y('" . $ColumnID . ") as " . $AsID;
	}
}



class CSVTrackingRecord
{
	public /* @var float	*/ $Speed;
	public /* @var float	*/ $CoordXLng;
	public /* @var float	*/ $CoordYLat;
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
		$this->CoordXLng	= ($Count >= 3) ? floatval($aCSVArray[2]) : 0;
		$this->CoordYLat	= ($Count >= 4) ? floatval($aCSVArray[3]) : 0;

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

	public function BeginUpdate ();
	public function EndUpdate ();
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


		///   Подготовка потребителя к добавлению записей. 
		$aConsumer->BeginUpdate();


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
		

		///   Завершение потребления. 
		$aConsumer->EndUpdate();


		return $RecordsCount;
	}
}



class CSVTrackingJobHelper
{
	///  Вспомогательный метод для обработки входящего запроса (с CSV-файлом) 
	///  для использования в Controller-классах. 
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



///   Отладочный потребитель CSV-данных. 
class CSVDebugConsumer implements ICSVTrackingConsumer
{
	private $Counter = 0;

	public function SetCapacity (int $aSize)
	{
	}

	public function PushRecord (CSVTrackingRecord $R)
	{
		if ($R->DateTime !== null)
			$this->Counter++;
	}

	public function GetCount ()
	{
		return $this->Counter;
	}

	public function BeginUpdate ()
	{
	}

	public function EndUpdate ()
	{
	}
}



///   Одна из реализаций настоящего потребителя CSV-данных. 
class CSVDatabaseConsumer implements ICSVTrackingConsumer
{
	public const DefaultBulkInsertStep = 256;

	private $Counter = 0;
	private $BufferArray = [];

	private $Worker_RouteID				= 0;
	private $Worker_BulkInsertCount		= self::DefaultBulkInsertStep;
	private $Worker_UseBulkInsert		= true;

	private $ExecutionStart;
	private $ExecutionEnd;
	public 	$ExecutionTime;



	public function __construct ($RouteID, $BulkInsertCount = self::DefaultBulkInsertStep, $UseBulkInsert = true)
	{
		$this->Worker_RouteID			= $RouteID;
		$this->Worker_BulkInsertCount	= $BulkInsertCount;
		$this->Worker_UseBulkInsert		= $UseBulkInsert;
	}

	

	public function SetCapacity (int $aSize)
	{
	}
	

	public function BeginUpdate ()
	{
		DB::connection()->disableQueryLog();
		
		$this->ExecutionStart = microtime(true);
		
		$this->BufferArray = [];
	}

	public function EndUpdate ()
	{
		$this->BulkInsert();

		$this->ExecutionEnd = microtime(true);
		$this->ExecutionTime = ($this->ExecutionEnd - $this->ExecutionStart);
		
		DB::connection()->enableQueryLog();
	}

	
	
	private function BulkInsert ()
	{
		if (!$this->Worker_UseBulkInsert) return;

		if (count($this->BufferArray) <= 0)
		{
			return;
		}

		///   Добавляем все данные из накопленного буфера. 
		///   Вносим в базу данных новые точки. 
		$NewPoint = \App\VehiclesWaypoint::insert($this->BufferArray);

		///   Очищаем буфер. 
		$this->BufferArray = [];
	}
	


	public function PushRecord (CSVTrackingRecord $R)
	{
		if (! ($R->DateTime !== null))
		{
			return false;
		}
		
		
		if ($this->Worker_UseBulkInsert)
		{
			///   Наполнение массива для операции bulk insert. 
			$this->Counter++;
			array_push($this->BufferArray,
			[
				WaypointData::RouteID	=> $this->Worker_RouteID,
				WaypointData::Time		=> $R->DateTime,
				WaypointData::Speed		=> $R->Speed,
				WaypointData::Coord		=> DB::raw(DBSpatialHelper::MakePointValue(
												$R->CoordXLng,
												$R->CoordYLat
										   )),
			]);


			if (count($this->BufferArray) >= $this->Worker_BulkInsertCount)
			{
				$this->BulkInsert();
			}
		}
		else 
		{
			\App\VehiclesWaypoint::create
			([
				WaypointData::RouteID	=> $this->Worker_RouteID,
				WaypointData::Time		=> $R->DateTime,
				WaypointData::Speed		=> $R->Speed,
				WaypointData::Coord		=> DB::raw(DBSpatialHelper::MakePointValue(
												$R->CoordXLng,
												$R->CoordYLat
										   )),
			]);
		}
	}



	public function GetCount ()
	{
		return $this->Counter;
	}
}



