<?php



///                                                                                            
///   Статичное описание модели данных, чтобы не использовать строки, которые 
///   гораздо труднее заменять автозаменой в случае изменения модели данных. 
///____________________________________________________________________________________________
///                                                                                            
///   Тип транспортного средства. 
///____________________________________________________________________________________________

///   Дополнительное описание модели, чтобы в столбцах базы данных были комментарии, а также
///   чтобы сразу понимать, какие поля за что отвечают, и в каких они единицах измерения. 

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

	use App\My\WaypointsAPI\Info	\VehiclesType as Info;
	

	
	class VehiclesType extends StructureSource
	{
		public const Name = 'vehicles'.'types';
		public const Table = self::DatabasePrefix . self::Name;
	
		public const ModelName			= 'ModelName';
		public const BrandName			= 'BrandName';
		public const Description 		= 'Description';
		public const VehicleClass		= 'VehicleClass';
		public const EngineVolume		= 'EngineVolume';
		public const ServicePeriod		= 'ServicePeriod';



		///   Повтор элементов с прямым обращением к таблице (для уточнения в Join запросах и т. д.) 
		public const Table_ALL				= self::Table .'.*';
		public const Table_ID				= self::Table .'.'. self::ID;
		public const Table_ModelName		= self::Table .'.'. self::ModelName;
		public const Table_BrandName		= self::Table .'.'. self::BrandName;
		public const Table_Description 		= self::Table .'.'. self::Description;
		public const Table_VehicleClass		= self::Table .'.'. self::VehicleClass;
		public const Table_EngineVolume		= self::Table .'.'. self::EngineVolume;
		public const Table_ServicePeriod	= self::Table .'.'. self::ServicePeriod;
		


		public const UseLaravelTimestamps = false;
		
		public static function ClearMigration ()
		{
			self::ClearTable(self::Table);
		}
		
		public static function ExecuteMigration ()
		{
	        Schema::create(self::Table, function (Blueprint $table) {

	        	$table->increments	(self::ID);

				$table->string		(self::ModelName);
				$table->string		(self::BrandName);
				$table->string		(self::Description);
				$table->integer		(self::VehicleClass);
				$table->integer		(self::EngineVolume)		->comment = Info::EngineVolume;
				$table->integer		(self::ServicePeriod)		->comment = Info::ServicePeriod;

	            //$table->timestamps();

	        });
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
				self::ID 		=> $F->numberBetween($min = 1, $max = 30),//$F->unique()->numberBetween($min = 1, $max = 30),

				self::ModelName			=> ucwords( $F->word() . ' ' . $F->randomLetter() . $F->numberBetween($min = 1, $max = 2000) ),
				self::BrandName			=> ucwords( $F->words($F->numberBetween($min = 1, $max = 2), true) ),
				self::Description		=> $F->sentence(10),
				self::VehicleClass		=> $F->randomElement([10, 11, 12, 13, 100, 101, 200]),
				self::EngineVolume		=> $F->numberBetween($min = 2, $max = 100) * 100,
				self::ServicePeriod		=> $F->numberBetween($min = 6, $max = 12 * 3),
			]);
		}

		public const FillableElements =
		[
			self::ModelName,
			self::BrandName,
			self::Description,
			self::VehicleClass,
			self::EngineVolume,
			self::ServicePeriod,
		];

		public const HiddenElements =
		[				
		];
	}
}



