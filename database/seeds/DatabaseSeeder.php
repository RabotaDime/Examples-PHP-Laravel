<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DeepCopy\TypeFilter\Date\DateIntervalFilter;

use App\My\WaypointsAPI\ID\VehiclesType			as ID_VehiclesType;
use App\My\WaypointsAPI\ID\Vehicle				as ID_Vehicle;
use App\My\WaypointsAPI\ID\VehiclesRoute		as ID_VehiclesRoute;
use App\My\WaypointsAPI\ID\VehiclesWaypoint		as ID_VehiclesWaypoint;

use App\My\WaypointsAPI\Data\VehiclesType		as Data_VehiclesType;
use App\My\WaypointsAPI\Data\Vehicle			as Data_Vehicle;
use App\My\WaypointsAPI\Data\VehiclesRoute		as Data_VehiclesRoute;
use App\My\WaypointsAPI\Data\VehiclesWaypoint	as Data_VehiclesWaypoint;



class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
		$F = Faker\Factory::create();


		///   Очистка данных. 
		Data_VehiclesWaypoint	::ClearMigration();
		Data_VehiclesRoute		::ClearMigration();
		Data_Vehicle			::ClearMigration();
		Data_VehiclesType		::ClearMigration();


		///   Создание типов транспортных средств (VehiclesTypes). 
		$VehicleTypesCount = 30;

		foreach (range(1, $VehicleTypesCount) as $Index)
		{
			App\VehiclesType::create(Data_VehiclesType::CreateFactoryDefinition($F,
			[
				ID_VehiclesType::ID => $Index,
			]));

			$F->unique(true); 
		}

		
		$F = Faker\Factory::create();

		///   Создание транспортных средств (Vehicles). 
		$VehiclesCount = 30;
		//$VehicleTypesData = App\VehiclesType::all();

		foreach (range(1, $VehiclesCount) as $Index)
		{
			App\Vehicle::create(Data_Vehicle::CreateFactoryDefinition($F,
			[
				ID_Vehicle::ID => $Index,
			]));

			$F->unique(true); 
		}


		$F = Faker\Factory::create();

		///   Создание тестовых путей (VehiclesWaypoint). 
		$RoutesCount = 5;
		$PointsCountSet = [10, 50, 10, 50, 100];
		$PointID = 1;

		foreach (range(1, $RoutesCount) as $RouteIndex)
		{
			App\VehiclesRoute::create(Data_VehiclesRoute:: CreateFactoryDefinition($F,
			[
				ID_VehiclesRoute::ID => $RouteIndex,
			]));

			$F->unique(true);

			$PointsCount = $PointsCountSet[$RouteIndex - 1];

			$PointsStartDate = $F->dateTimeBetween('-5 years', '+0 days');

			foreach (range(1, $PointsCount) as $Index)
			{
				$PointsDate = clone $PointsStartDate;
				$RandomSecs = $Index * 60 * $F->numberBetween($min = 1, $max = 10);
				$PointsDate->add(DateInterval::createFromDateString("$RandomSecs seconds"));

				$F2 = Faker\Factory::create();

				App\VehiclesWaypoint::create(Data_VehiclesWaypoint::CreateFactoryDefinition($F2,
				[
					'ID' => $PointID,

					'RouteID'		=> $RouteIndex, // $F->randomElement(range(1, $RoutesCount))
					'Time'			=> clone $PointsDate,
				]));

				$F2->unique(true);

				$PointID++;
			}
		}


        // $this->call(UsersTableSeeder::class);
    }
}

