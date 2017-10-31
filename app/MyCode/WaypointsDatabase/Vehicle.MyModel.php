<?php



///                                                                                            
///   Статичное описание модели данных, чтобы не использовать строки, которые 
///   гораздо труднее заменять автозаменой в случае изменения модели данных. 
///____________________________________________________________________________________________
///                                                                                            
///   Транспортное средство. 
///____________________________________________________________________________________________

///   Дополнительное описание модели, чтобы в столбцах базы данных были комментарии, а также
///   чтобы сразу понимать, какие поля за что отвечают, и в каких они единицах измерения. 

namespace App\My\WaypointsAPI\Info { class Vehicle
{
	public const Mileage	= 'Mileage (minutes)';
}}

namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Faker\Generator as Faker;

	use App\My\WaypointsAPI\Info	\Vehicle as Info;



	class Vehicle extends StructureSource
	{
		public const Name = 'vehicles';
		public const Table = self::DatabasePrefix . self::Name;

		public const TypeID				= 'TypeID';
		public const Mileage			= 'Mileage';
		public const Description 		= 'Description';
		public const LastServiceTime	= 'LastServiceTime';

		public const TypeID_Key			= VehiclesType::ID;
		public const TypeID_Source		= VehiclesType::Table;



		///   Повтор элементов с прямым обращением к таблице (для уточнения в Join запросах и т. д.) 
		public const Table_ALL				= self::Table .'.*';
		public const Table_ID				= self::Table .'.'. self::ID;
		public const Table_TypeID			= self::Table .'.'. self::TypeID;
		public const Table_Mileage			= self::Table .'.'. self::Mileage;
		public const Table_Description 		= self::Table .'.'. self::Description;
		public const Table_LastServiceTime	= self::Table .'.'. self::LastServiceTime;



		public const UseLaravelTimestamps = false;
		
		public static function ExecuteMigration ()
		{
	        Schema::create(self::Table, function (Blueprint $table) {

	        	$table->increments	(self::ID);

	            $table->integer		(self::TypeID)->unsigned();										///  FKEY! 
	            $table->integer		(self::Mileage)				->comment = Info::Mileage; 			///  Пробег в минутах. 
	            $table->string		(self::Description);												///  Дополнительное описание. 
	            $table->datetime	(self::LastServiceTime);											///  Последнее обслуживание. 

				$table->foreign		(self::TypeID)
									 -> references	(self::TypeID_Key)
									 -> on			(self::TypeID_Source);

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

				self::Mileage			=> $F->randomNumber(),
				self::Description		=> $F->realText(100),
				self::LastServiceTime	=> $F->dateTimeBetween('-5 years', '+0 days'),

				self::TypeID			=> factory(\App\VehiclesType::class)->make()->ID,
			]);
		}

		public const FillableElements =
		[
			self::TypeID,
			self::Mileage,
			self::Description,
			self::LastServiceTime,
		];

		public const HiddenElements =
		[				
		];
	}
}



