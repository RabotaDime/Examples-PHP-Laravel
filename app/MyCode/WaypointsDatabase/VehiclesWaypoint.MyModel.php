<?php



///                                                                                                 
///   Точка пути. 
///_________________________________________________________________________________________________

namespace App\My\WaypointsAPI\ID { class VehiclesWaypoint extends Source
{
	public const Name = 'vehicles'.'waypoints';
	public const Table = API::DatabasePrefix . self::Name;

	public const RouteID		= 'RouteID';
	public const Time			= 'Time';
	public const Speed			= 'Speed';
	public const Coord			= 'Coord';
	public const CoordXLng		= 'CoordXLng';
	public const CoordYLat		= 'CoordYLat';

	public const RouteID_Key		= VehiclesRoute::ID;
	public const RouteID_Source		= VehiclesRoute::Table;
}}

namespace App\My\WaypointsAPI\Info { class VehiclesWaypoint
{
	public const Speed			= 'Speed';
}}



namespace App\My\WaypointsAPI\Data
{
	use Illuminate\Support\Facades\Schema;
	use Illuminate\Database\Schema\Blueprint;
	use Faker\Generator as Faker;

	use App\My\WaypointsAPI\ID		\VehiclesWaypoint as ID;
	//use App\My\WaypointsAPI\Info	\VehiclesWaypoint as Info;



	class VehiclesWaypoint extends StructureSource
	{
		public const UseLaravelTimestamps = false;

		public static function ClearMigration ()
		{
			self::ClearTable(ID::Table);
		}

		public static function ExecuteMigration ()
		{
	        Schema::create(ID::Table, function (Blueprint $table) {

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
	    		$table->bigIncrements	(ID::ID);

	    		$table->integer			(ID::RouteID)->unsigned();	///  FKEY! 
	    		$table->dateTime		(ID::Time);
				$table->float			(ID::Speed, 8, 3);
	    		$table->point			(ID::Coord);
	    		//$table->decimal		('CoordXLng', 11, 8);		///  Долгота, Longitude   -180.###### +180.######
	    		//$table->decimal		('CoordYLat', 10, 8);		///  Широта, (f)Latitude   -90.######  +90.######

	    		$table->foreign			(ID::RouteID)->references(ID::RouteID_Key)->on(ID::RouteID_Source)->onDelete('cascade');


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
				ID::ID			=> abs($F->randomNumber()),//abs($F->unique()->randomNumber()),

				ID::Time		=> $F->dateTimeBetween('-5 years', '+0 days'),
				ID::Speed		=> $F->randomFloat(0, $min = 0.0, $max = 100.0),

								///   Создаю случайную точку пути. 
				ID::Coord		=> \App\VehiclesWaypoint::MakePoint($F->longitude(), $F->latitude()),
			]);
		}

		public const FillableElements =
		[
			ID::Time,
			ID::Speed,
			ID::Coord,
		];

		public const HiddenElements =
		[				
		];
	}
}



