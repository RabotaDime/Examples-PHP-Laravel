<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\My\WaypointsAPI\ID		\Vehicle as ID;
use App\My\WaypointsAPI\Data	\Vehicle as Data;



class Vehicle extends Model
{
    protected $table = ID::Table;

	public $timestamps = Data::UseLaravelTimestamps;

	protected $fillable	= Data::FillableElements;		
	protected $hidden	= Data::HiddenElements;
	


	public function types ()
	{
		return $this->hasMany(VehiclesType::class);
	}

	public function waypoints (int $VehicleID = 1)
	{
		///   Прямой запрос данных по точкам пути с извлечением Spatial-значений. 
		return  DB::table('api_vehicleswaypoints')
					->select(DB::raw("Time, Speed, X('Coord') as CoordXLng, Y('Coord') as CoordYLat"))
					->where('VehicleID', '=', $VehicleID)
					->get();

/*
        В данном случае, я полагаюсь на Laravel, чтобы не расставлять биндинги самостоятельно. 
        Кроме того, данный запрос имеет только один входной параметр, который типизирован 
        через type hinting. Поэтому его можно не усложнять ради безопасности входных аргументов. 
        
		return  DB::table('api_vehicleswaypoints')
					->select(DB::raw("Time, Speed, X('Coord') as CoordXLng, Y('Coord') as CoordYLat"))
					->whereId(DB::raw(':vid'))
					->setBindings
					([
						'vid' => $VehicleID,
					])
					->get();

*/
	}
}

