<?php



///                                                                                            
///   Статичное описание модели данных, чтобы не использовать строки, которые 
///   гораздо труднее заменять автозаменой в случае изменения модели данных. 
///____________________________________________________________________________________________
///                                                                                            
///   Путь. 
///_________________________________________________________________________________________________

///   Дополнительное описание модели, чтобы в столбцах базы данных были комментарии, а также
///   чтобы сразу понимать, какие поля за что отвечают, и в каких они единицах измерения. 

namespace App\My\WaypointsAPI\Info { class VehiclesRoute
{
}}

namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Faker\Generator as Faker;

	//use App\My\WaypointsAPI\Info	\VehiclesRoute as Info;



	class VehiclesRoute extends StructureSource
	{
		public const Name = 'vehicles'.'routes';
		public const Table = self::DatabasePrefix . self::Name;
	
		public const VehicleID		= 'VehicleID';
		public const Description	= 'Description';
	
		public const VehicleID_Key		= Vehicle::ID;
		public const VehicleID_Source	= Vehicle::Table;



		///   Повтор элементов с прямым обращением к таблице (для уточнения в Join запросах и т. д.) 
		public const Table_ALL				= self::Table .'.*';
		public const Table_ID				= self::Table .'.'. self::ID;
		public const Table_VehicleID		= self::Table .'.'. self::VehicleID;
		public const Table_Description 		= self::Table .'.'. self::Description;

    
    
		public const UseLaravelTimestamps = false; 
		
		public static function ExecuteMigration ()
		{
	        Schema::create(self::Table, function (Blueprint $table) {

	        	$table->increments	(self::ID);

            	$table->integer		(self::VehicleID)->unsigned()->nullable(); ///  FKEY! NULL 
	        	$table->string		(self::Description);

				$table->foreign		(self::VehicleID)
									 -> references	(self::VehicleID_Key)
									 -> on			(self::VehicleID_Source);

	            //$table->timestamps();

	        });
		}
			
		public static function ClearMigration ()
		{
			self::ClearTable(self::Table);
		}

		public static function ReverseMigration ()
		{
    		Schema::dropIfExists(self::Table);
		}
		
		public static function CreateFactoryFunction ()
		{
			return function (Faker $F) { return self::CreateFactoryDefinition($F); };
		}

		public static function CreateFactoryDefinition (Faker $F, array $SpecifyThis = [])
		{
			return self::Specify($SpecifyThis,
			[
				self::ID				=> $F->numberBetween($min = 1, $max = 30),//$F->unique()->numberBetween($min = 1, $max = 30),

				self::Description		=> $F->sentence(10),

				self::VehicleID		=> factory(\App\Vehicle::class)->make()->ID,
			]);
		}

		public const FillableElements =
		[
			self::VehicleID,
			self::Description,
		];

		public const HiddenElements =
		[				
		];
	}
}



