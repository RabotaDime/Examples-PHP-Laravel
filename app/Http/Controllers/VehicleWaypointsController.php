<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;

//use App\MyCode\CSVTrackingJobFile;
use App\MyCode\CSVTrackingJobHelper;
use App\MyCode\DebugCSVConsumer;



class VehicleWaypointsController extends Controller
{
		///  Идентификатор входящего загружаемого файла. 
	public const ImportCSVFileID = 'datafile';



	public function home ()
	{
		return view('vehicles.index');
	}



	public function show ($vehicle_id, $route_id)
	{
		return view('vehicles.routes.show');
	}

	public function api_show (Request $aR, $vehicle_id, $route_id)
	{
		return "API show $vehicle_id + $route_id";
	}



    public function import (Request $aR)
	{
/*
		dd($aR->all());

		///  Максимальный размер файла CSV = 50 МБ.
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

