<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Exception;

//use App\My\CSVTrackingJobFile;
use App\My\CSVTrackingJobHelper;
use App\My\CSVDebugConsumer;
use App\My\CSVDatabaseConsumer;

use App\Vehicle as VehicleModel;

use App\My\WaypointsAPI\Data\VehiclesType		as VehiclesType;
use App\My\WaypointsAPI\Data\Vehicle			as Vehicle;
use App\My\WaypointsAPI\Data\VehiclesRoute		as VehiclesRoute;
use App\My\WaypointsAPI\Data\VehiclesWaypoint	as VehiclesWaypoint;




class VehicleWaypointsController extends Controller
{
		///  Идентификатор входящего загружаемого файла. 
	public const ImportCSVFileID = 'datafile';



	public function home ()
	{
/*
        ///  Все транспортные средства. 
		$Vehicles = Vehicle::all();

		///  Все транспортные средства разделенные по 5 единиц. 
		$Vehicles = Vehicle::paginate(5);

		///  Пример присоединения и вывода модели/бренда. 
		///  Дополнительно выводятся все пути. 
		$Vehicles =	DB::table(Vehicle::Table)->
			join (VehiclesType	::Table, Vehicle::Table_TypeID	, '=', VehiclesType::Table_ID)->
			join (VehiclesRoute	::Table, Vehicle::Table_ID		, '=', VehiclesRoute::Table_VehicleID)->
			select
			(
				Vehicle::Table_ALL,
				VehiclesType::BrandName,
				VehiclesType::ModelName,
				VehiclesRoute::Table_ID . ' as RouteID'
			)->
			paginate(10);
*/

		///  Подсчет количества путей у каждой машины + вывод первого пути. 
		$Vehicles = DB::table(Vehicle::Table)->
		select( DB::raw
		(
			Vehicle::Table_ID . ' as VehicleID,' .
			VehiclesType::Table_BrandName . ' as VehicleBrand,' .
			VehiclesType::Table_ModelName . ' as VehicleModel,' .
			VehiclesRoute::Table_ID . ' as FirstRouteID,' .
			'count(' . VehiclesRoute::Table_ID . ') as TotalRoutes'
		))->
		join (VehiclesRoute	::Table, Vehicle::Table_ID		, '=', VehiclesRoute::Table_VehicleID)->
		join (VehiclesType	::Table, Vehicle::Table_TypeID	, '=', VehiclesType::Table_ID)->
		groupBy ('VehicleID')->
		paginate(10);

		$VehiclesCount = 0;

		if (method_exists($Vehicles, 'count'))
			$VehiclesCount = $Vehicles->count();

		return view('vehicles.index', compact('Vehicles', 'VehiclesCount'));
	}



	public function api_show (Request $aR, $vehicle_id, $route_id)
	{
		return
		[
			'API'			=> true,
			'VehicleID'		=> $vehicle_id,
			'RouteID'		=> $route_id,
			'ResponseText'	=> "Путь [$route_id] для машины [$vehicle_id]",
		];
	}

	public function show_list ($vehicle_id)
	{
		return "Отладка маршрута: Все пути для машины [$vehicle_id]";
		return view('vehicles.routes.show');
	}

	public function show ($vehicle_id, $route_id)
	{
		return "Отладка маршрута: Путь [$route_id] для машины [$vehicle_id]";
		return view('vehicles.routes.show');
	}



	public function api_import_delayed (Request $R, $vehicle_id)
	{
		///   Сохраняем файл во временное хранилище, для последующей отложенной  
		///   обработки в Laravel Queue, с показом хода выполнения на клиенте. 
		$StoredJobFile = CSVTrackingJobHelper::StoreRequestFile($R, self::ImportCSVFileID);

		// TODO : Реализовать событие и Queue-обработчик. 
		abort(501, 'Not implemented now. Please try later.');
	}

	public function import_delayed ($vehicle_id)
	{
		return "Отладка маршрута: Динамический импорт CSV-данных для машины [$vehicle_id]";
	}



	///   POST метод для формы загрузки CSV-файла. 
	public function api_import_direct (Request $R, $vehicle_id)
	{
		///   Создаем новую путевую запись для указанного транспорта. 
		$NewRoute = \App\VehiclesRoute::create
		([
			'VehicleID'		=> $vehicle_id,
			'Description'	=> 'Imported Route (api_import_direct)',
		]);

		///   Создаем потребителя, который запишет данные в БД, с помощью массовой вставки (bulk insert). 
		$ImportConsumer = new CSVDatabaseConsumer($NewRoute->id, CSVDatabaseConsumer::DefaultBulkInsertStep, true);


		///   Начинаем обработку запроса. 
		try
		{
			$ImportResult = CSVTrackingJobHelper::ProcessRequest($R, self::ImportCSVFileID, $ImportConsumer);
			$ImportErrorMessage = '';
		}
		catch (Exception $E)
		{
			$ImportResult = -1;
			$ImportErrorMessage = "Error: \"" . $E->getMessage() . "\"";
		}

		if ($ImportResult >= 0)
		{
			return [
				'ImportResult' => [
					'ErrorCode'			=> 0,
					'Message'			=> 'Successful import operation.',
					'RecordsReaded'		=> $ImportResult,
					'RecordsAdded'		=> $ImportConsumer->GetCount(),
					'ExecutionTime'		=> $ImportConsumer->ExecutionTime,
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
					'ExecutionTime'		=> 'fail',
				]
            ];
		}
	}

	public function import_direct ($vehicle_id)
	{
		//return "Отладка маршрута: Обычный импорт CSV-данных для машины [$vehicle_id]";

		$Vehicle		= \App\Vehicle::find($vehicle_id);
		$ImportMethod	= 'direct';

		return view('vehicles.routes.import', compact('ImportMethod', 'Vehicle'));
	}



    public function api_import_debug (Request $R)
	{
/*
		dd($R->all());

		///  Максимальный размер файла, который мы будем считать допустимым CSV = 50 МБ.
		$FileMaxSize = 50 * 1024 * 1024;

		$ValidateData = $R->validate([
			"$FileID" => "required|size:$FileMaxSize",
		]);
*/

		$ImportConsumer = new CSVDebugConsumer();

		try
		{
			$ImportResult = CSVTrackingJobHelper::ProcessRequest($R, self::ImportCSVFileID, $ImportConsumer);
			$ImportErrorMessage = '';
		}
		catch (Exception $E)
		{
			$ImportResult = -1;
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

    public function import_debug ($vehicle_id)
	{
		return "Отладка маршрута: Отладочный импорт CSV-данных для машины [$vehicle_id]";
    }
}

