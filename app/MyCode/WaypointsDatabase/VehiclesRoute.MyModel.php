<?php



///                                                                                                 
///   Путь. 
///_________________________________________________________________________________________________

namespace App\My\WaypointsAPI\ID { class VehiclesRoute extends Source
{
	public const Name = 'vehicles'.'routes';
	public const Table = API::DatabasePrefix . self::Name;

	public const VehicleID		= 'VehicleID';
	public const Description	= 'Description';

	public const VehicleID_Key		= Vehicle::ID;
	public const VehicleID_Source	= Vehicle::Table;	
}}

namespace App\My\WaypointsAPI\Info { class VehiclesRoute
{
}}



namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Faker\Generator as Faker;

	use App\My\WaypointsAPI\ID		\VehiclesRoute as ID;
	//use App\My\WaypointsAPI\Info	\VehiclesRoute as Info;



	class VehiclesRoute extends StructureSource
	{
		public const UseLaravelTimestamps = false;
		
		public static function ExecuteMigration ()
		{
	        Schema::create(ID::Table, function (Blueprint $table) {

	        	$table->increments	(ID::ID);

            	$table->integer		(ID::VehicleID)->unsigned()->nullable(); ///  FKEY! NULL 
	        	$table->string		(ID::Description);

				$table->foreign		(ID::VehicleID)
									 -> references	(ID::VehicleID_Key)
									 -> on			(ID::VehicleID_Source);

	            //$table->timestamps();

	        });
		}
			
		public static function ClearMigration ()
		{
			self::ClearTable(ID::Table);
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
				ID::ID				=> $F->numberBetween($min = 1, $max = 30),//$F->unique()->numberBetween($min = 1, $max = 30),

				ID::Description		=> $F->sentence(10),

				ID::VehicleID		=> factory(\App\Vehicle::class)->make()->ID,
			]);
		}

		public const FillableElements =
		[
			ID::Description,
		];

		public const HiddenElements =
		[				
		];
	}
}



