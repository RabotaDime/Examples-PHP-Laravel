<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\MyCode\CSVTrackingJobFile;
use App\MyCode\CSVTrackingJobHelper;
use App\MyCode\DebugCSVConsumer;



class VehicleWaypointsController extends Controller
{
		///  ������������� ��������� ������������ �����. 
	public const ImportCSVFileID = 'datafile';



	public function index ()
	{
		return view('vehicles.routes.index');
	}



	public function show ($vehicle_id, $route_id)
	{
	}

	public function api_show ($vehicle_id, $route_id)
	{
	}



    public function import (Request $aR)
	{
/*
		dd($aR->all());

		///  ������������ ������ ����� CSV = 50 ��.
		$FileMaxSize = 50 * 1024 * 1024;

		$ValidateData = $R->validate([
			"$FileID" => "required|size:$FileMaxSize",
		]);
*/

		$ImportConsumer = new DebugCSVConsumer();

		try
		{
			$ImportResult = CSVTrackingJobHelper::ProcessRequest($aR, self::ImportCSVFileID, $ImportConsumer);
			$ImportErrorMessage = '';
		}
		catch (Exception $E)
		{
			$ImportErrorMessage = "Error: \"$E->getMessage\"";
		}


		if ($ImportResult >= 0)
		{
			return [
				'ImportResult' => [
					'ErrorCode'			=> 0,
					'Message'			=> 'Successful import operation.',
					'RecordsReaded'		=> $ImportResult,
					'RecordsAdded'		=> $ImportConsumer->GetCount(),
				]
            ];
		}
		else
		{
			return [
				'ImportResult' => [
					'ErrorCode'			=> $ImportResult,
					'Message'			=> 'Import operation failed. ' . $ImportErrorMessage,
					'RecordsReaded'		=> 0,
					'RecordsAdded'		=> 0,
				]
            ];
		}
	}
}

