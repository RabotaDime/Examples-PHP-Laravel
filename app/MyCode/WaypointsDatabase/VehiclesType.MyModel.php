<?php



///                                                                                                 
///   Тип транспортного средства. 
///_________________________________________________________________________________________________

namespace App\My\WaypointsAPI\ID { class VehiclesType extends Source
{
	public const Name = 'vehicles'.'types';
	public const Table = API::DatabasePrefix . self::Name;

	public const ModelName			= 'ModelName';
	public const BrandName			= 'BrandName';
	public const Description 		= 'Description';
	public const VehicleClass		= 'VehicleClass';
	public const EngineVolume		= 'EngineVolume';
	public const ServicePeriod		= 'ServicePeriod';
}}

namespace App\My\WaypointsAPI\Info { class VehiclesType
{
	public const EngineVolume		= 'Engine volume (mL)';
	public const ServicePeriod		= 'Recommended service period (in months)';
}}



namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Faker\Generator as Faker;

	use App\My\WaypointsAPI\ID		\VehiclesType as ID;
	use App\My\WaypointsAPI\Info	\VehiclesType as Info;
	

	
	class VehiclesType extends StructureSource
	{
		public const UseLaravelTimestamps = false;
		
		public static function ClearMigration ()
		{
			self::ClearTable(ID::Table);
		}
		
		public static function ExecuteMigration ()
		{
	        Schema::create(ID::Table, function (Blueprint $table) {

	        	$table->increments	(ID::ID);

				$table->string		(ID::ModelName);
				$table->string		(ID::BrandName);
				$table->string		(ID::Description);
				$table->integer		(ID::VehicleClass);
				$table->integer		(ID::EngineVolume)		->comment = Info::EngineVolume;
				$table->integer		(ID::ServicePeriod)		->comment = Info::ServicePeriod;

	            //$table->timestamps();

	        });
		}

		public static function ReverseMigration ()
		{
    		Schema::dropIfExists(ID::Table);
		}

		public static function CreateFactoryFunction ()
		{
			return function (Faker $F) { return self::CreateFactoryDefinition($F); };
		}

		public static function CreateFactoryDefinition (Faker $F, array $SpecifyThis = [])
		{
			return self::Specify($SpecifyThis,
			[
				ID::ID 		=> $F->numberBetween($min = 1, $max = 30),//$F->unique()->numberBetween($min = 1, $max = 30),

				ID::ModelName		=> ucwords( $F->word() . ' ' . $F->randomLetter() . $F->numberBetween($min = 1, $max = 2000) ),
				ID::BrandName		=> ucwords( $F->words($F->numberBetween($min = 1, $max = 2), true) ),
				ID::Description		=> $F->sentence(10),
				ID::VehicleClass	=> $F->randomElement([10, 11, 12, 13, 100, 101, 200]),
				ID::EngineVolume	=> $F->numberBetween($min = 2, $max = 100) * 100,
				ID::ServicePeriod	=> $F->numberBetween($min = 6, $max = 12 * 3),
			]);
		}

		public const FillableElements =
		[
			ID::ModelName,
			ID::BrandName,
			ID::Description,
			ID::VehicleClass,
			ID::EngineVolume,
			ID::ServicePeriod,
		];

		public const HiddenElements =
		[				
		];
	}
}



