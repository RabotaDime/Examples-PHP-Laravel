<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use DeepCopy\TypeFilter\Date\DateIntervalFilter;

use App\My\WaypointsAPI\Data\VehiclesType		as VehiclesType;
use App\My\WaypointsAPI\Data\Vehicle			as Vehicle;
use App\My\WaypointsAPI\Data\VehiclesRoute		as VehiclesRoute;
use App\My\WaypointsAPI\Data\VehiclesWaypoint	as VehiclesWaypoint;



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
		VehiclesWaypoint	::ClearMigration();
		VehiclesRoute		::ClearMigration();
		Vehicle				::ClearMigration();
		VehiclesType		::ClearMigration();


		///   Создание типов транспортных средств (VehiclesTypes). 
		$VehicleTypesCount = 30;

		foreach (range(1, $VehicleTypesCount) as $Index)
		{
			App\VehiclesType::create(VehiclesType::CreateFactoryDefinition($F,
			[
				VehiclesType::ID => $Index,
			]));

			$F->unique(true); 
		}

		
		$F = Faker\Factory::create();

		///   Создание транспортных средств (Vehicles). 
		$VehiclesCount = 30;
		//$VehicleTypesData = App\VehiclesType::all();

		foreach (range(1, $VehiclesCount) as $Index)
		{
			App\Vehicle::create(Vehicle::CreateFactoryDefinition($F,
			[
				Vehicle::ID => $Index,
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
			App\VehiclesRoute::create(VehiclesRoute:: CreateFactoryDefinition($F,
			[
				VehiclesRoute::ID => $RouteIndex,
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

				App\VehiclesWaypoint::create(VehiclesWaypoint::CreateFactoryDefinition($F2,
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

