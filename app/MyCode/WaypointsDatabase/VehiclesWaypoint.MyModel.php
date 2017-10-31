<?php



///                                                                                            
///   Статичное описание модели данных, чтобы не использовать строки, которые 
///   гораздо труднее заменять автозаменой в случае изменения модели данных. 
///____________________________________________________________________________________________
///                                                                                            
///   Точка пути. 
///____________________________________________________________________________________________

///   Дополнительное описание модели, чтобы в столбцах базы данных были комментарии, а также
///   чтобы сразу понимать, какие поля за что отвечают, и в каких они единицах измерения. 

namespace App\My\WaypointsAPI\Info { class VehiclesWaypoint
{
	public const Speed	= 'Speed (km/h)'; ///   TODO: Уточнить, описывают ли CSV данные скорость в километрах. 
}}

namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\DB;
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Faker\Generator as Faker;

	use App\My\DBSpatialHelper;

	use App\My\WaypointsAPI\Info	\VehiclesWaypoint as Info;



	class VehiclesWaypoint extends StructureSource
	{
		public const Name = 'vehicles'.'waypoints';
		public const Table = self::DatabasePrefix . self::Name;
	
		public const RouteID		= 'RouteID';
		public const Time			= 'Time';
		public const Speed			= 'Speed';
		public const Coord			= 'Coord';
		public const CoordXLng		= 'CoordXLng';
		public const CoordYLat		= 'CoordYLat';
	
		public const RouteID_Key		= VehiclesRoute::ID;
		public const RouteID_Source		= VehiclesRoute::Table;



		///   Повтор элементов с прямым обращением к таблице (для уточнения в Join запросах и т. д.) 
		public const Table_ALL				= self::Table .'.*';
		public const Table_ID				= self::Table .'.'. self::ID;
		public const Table_RouteID			= self::Table .'.'. self::RouteID;
		public const Table_Time				= self::Table .'.'. self::Time;
		public const Table_Speed			= self::Table .'.'. self::Speed;
		public const Table_Coord			= self::Table .'.'. self::Coord;
		public const Table_CoordXLng		= self::Table .'.'. self::CoordXLng;
		public const Table_CoordYLat		= self::Table .'.'. self::CoordYLat;
		


		public const UseLaravelTimestamps = false;

		public static function ClearMigration ()
		{
			self::ClearTable(self::Table);
		}

		public static function ExecuteMigration ()
		{
	        Schema::create(self::Table, function (Blueprint $table) {

				///
				///  В данном примере я рассматриваю, что это поле не нужно. 
				///  Но из-за особенностей работы баз данных и теоретической необходимости 
				///  в будущем как-то манипулировать точками пути, я просто сделаю это поле
				///  большим, раз точек будет очень много. Манипуляция с точками может 
				///  потребоваться, например, для удаления нескольких ошибочных точек,
				///  которые выбиваются из пути из-за сбоя оборудования. То есть, по сути 
				///  идентификаторы нужны. Но требуется решить на будущее, каким образом
				///  сохранить такое количество путевых точек в рамках одной таблицы. 
				///  
				///  Предполагаю использовать для сохранения путей Spatial Geometry типы 
				///  данных. Примерную мгновенную скорость можно расчитать из точек, если 
				///  у них будут отмечены временные метки, которые для каждой точеки пути 
				///  можно попробовать сохранить в BLOB/VARBINARY типах. Это усложнит 
				///  поиск по временным меткам. Но это можно частично обойти, если фрагментировать 
				///  куски путей по определенным временным промежуткам. Например сохранять
				///  пути кусками по 1 часу в одной строке. Тогда запасов одной таблицы
				///  должно хватить на большее число данных. А выборка статических данных
				///  о пути будет даже проще.
				///  
	    		$table->bigIncrements	(self::ID);

	    		$table->integer			(self::RouteID)->unsigned();	///  FKEY! 
	    		$table->dateTime		(self::Time);
				$table->float			(self::Speed, 8, 3)->comment(Info::Speed);
	    		$table->point			(self::Coord);
	    		//$table->decimal		('CoordXLng', 11, 8);		///  Долгота, Longitude   -180.###### +180.######
	    		//$table->decimal		('CoordYLat', 10, 8);		///  Широта, (f)Latitude   -90.######  +90.######

	    		$table->foreign			(self::RouteID)->references(self::RouteID_Key)->on(self::RouteID_Source)->onDelete('cascade');


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
				self::ID			=> abs($F->randomNumber()),//abs($F->unique()->randomNumber()),

				self::Time		=> $F->dateTimeBetween('-5 years', '+0 days'),
				self::Speed		=> $F->randomFloat(0, $min = 0.0, $max = 100.0),

								///   Создаю случайную точку пути. 
				self::Coord		=> DB::raw( DBSpatialHelper::MakePointValue($F->longitude(), $F->latitude()) ),
			]);
		}

		public const FillableElements =
		[
			self::RouteID,
			self::Time,
			self::Speed,
			self::Coord,
		];

		public const HiddenElements =
		[				
		];
	}
}



