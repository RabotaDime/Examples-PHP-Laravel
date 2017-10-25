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

		///  ��������� ������ � ������������� �������� �� ���������, 
		///  � ������ ���������/������� ������. 
		$this->DateTime		= null;
		$this->Speed		= ($Count >= 2) ? floatval($aCSVArray[1]) : 0;
		$this->CoordLong	= ($Count >= 3) ? floatval($aCSVArray[2]) : 0;
		$this->CoordFlat	= ($Count >= 4) ? floatval($aCSVArray[3]) : 0;

		///  �������� ���������� � ���������� ����. 
		$this->SetDateFromString( ($Count >= 1) ? $aCSVArray[0] : self::EmptyDateTimeString );

/*
		��������� �������
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
	///   ������������ ������ ��������� ������ � ��������. 
	public const MaximumRecordLength = 1000;

	///   ����������� ������ � ��������� ������ (���� ������). 
	public const RecordDelimiter = ';';



	public const InvalidCountResult			= -1;
	public const InvalidStreamResult		= -10;
	public const InvalidConsumerObject		= -20;



	public static function IsValidStream ($aFile)
	{
		///  �������� ������ ��� �������, ���������� � ���� ����-����������. 
		$ResourceType = get_resource_type($aFile) or
			die("CSVJobFile :: Unable to validate stream.");

		$ValidTypes = ['file', 'stream'];
		$ValidFileHandle = false;

		///  ������� ���������� ���� ������� ����� ����-���������. 
		foreach ($ValidTypes as $ValidType)
			if ($ResourceType == $ValidType)
			{
				$ValidFileHandle = true;
				break;
			}

		if
		(
			///  ���� ��� ������� �������� ������ � ����-���������. 
			( ($StatusResult = fstat($aFile)) !== false ) &&
			///  � ���� ���������� ������ � ����� ����-��������� ������ ����. 
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
			///  ���� ������ ����� �� �������� �� ������ ����, ���� �������� ���������. 
			return self::InvalidStreamResult;
		}

		if
		(
			///  ���� ������ ������������ ������
			empty($aConsumer) ||
			///  ��� ������ �� ��������� � ������� ���� (� PHP 7 ��� �������� �� �����)
			( !($aConsumer instanceof ICSVTrackingConsumer) )
		)
		{
			return self::InvalidConsumerObject;
		}



		$Record = new CSVTrackingRecord();
		$RecordsCount = 0;

		while
		(
			///  ��������� ������ ������ �� ����� 
			($DataRecord = fgetcsv
			(
				$aFileStream,
				self::MaximumRecordLength,
				self::RecordDelimiter
			))
			///  �� ��� ���, ���� ������� �� ������ ������������� ��������� 
			///  (��-�� ���������� ����� ����� ��� ���� ������). 
			!== false
		)
		{
			///  ���� ������ ������� ����������. 
			if ($Record->AssignFromCSVArray($DataRecord))
			{
				///  �������� ������ � �����������. 
				$aConsumer->PushRecord($Record);
				///  ����������� ������� ����������� �������. 
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



	///  ������� ��� ���������� ��������� ����� ��� ����������� ���������. 
	public static function StoreRequestFile ($aR, $aFileID)
	{
		if ($aR->hasFile($aFileID))
		{
			$RequestInputFile = $aR->file($aFileID);

			///  ��������� ��������� ��� �����. 
			$TempFileName = uniqid('queued-job-', true) . '.csv';

			///  ������������� ������������ ���������. 
			$StorageDisk = 'local';

			///   �������� �������� ���� � ������� ������ ��������� ������. 
			///   ������������ ����������� ����� Laravel "Storage". 
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



