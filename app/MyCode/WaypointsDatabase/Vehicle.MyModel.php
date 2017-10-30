<?php



///                                                                                                 
///   Транспортное средство. 
///_________________________________________________________________________________________________

namespace App\My\WaypointsAPI\ID { class Vehicle extends Source
{
	public const Name = 'vehicles';
	public const Table = API::DatabasePrefix . self::Name;

	public const TypeID				= 'TypeID';
	public const Mileage			= 'Mileage';
	public const Description 		= 'Description';
	public const LastServiceTime	= 'LastServiceTime';

	public const TypeID_Key			= VehiclesType::ID;
	public const TypeID_Source		= VehiclesType::Table;
}}

namespace App\My\WaypointsAPI\Info { class Vehicle
{
	public const Mileage			= 'Mileage (minutes)';
}}



namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Faker\Generator as Faker;

	use App\My\WaypointsAPI\ID		\Vehicle as ID;
	use App\My\WaypointsAPI\Info	\Vehicle as Info;



	class Vehicle extends StructureSource
	{
		public const UseLaravelTimestamps = false;
		
		public static function ExecuteMigration ()
		{
	        Schema::create(ID::Table, function (Blueprint $table) {

	        	$table->increments	(ID::ID);

	            $table->integer		(ID::TypeID)->unsigned();										///  FKEY! 
	            $table->integer		(ID::Mileage)				->comment = Info::Mileage; 			///  Пробег в минутах. 
	            $table->string		(ID::Description);												///  Дополнительное описание. 
	            $table->datetime	(ID::LastServiceTime);											///  Последнее обслуживание. 

				$table->foreign		(ID::TypeID)
									 -> references	(ID::TypeID_Key)
									 -> on			(ID::TypeID_Source);

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

				ID::Mileage			=> $F->randomNumber(),
				ID::Description		=> $F->realText(100),
				ID::LastServiceTime	=> $F->dateTimeBetween('-5 years', '+0 days'),

				ID::TypeID			=> factory(\App\VehiclesType::class)->make()->ID,
			]);
		}

		public const FillableElements =
		[
			ID::Mileage,
			ID::Description,
			ID::LastServiceTime,
		];

		public const HiddenElements =
		[				
		];
	}
}



