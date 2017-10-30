<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\My\WaypointsAPI\ID		\VehiclesWaypoint as ID;
use App\My\WaypointsAPI\Data	\VehiclesWaypoint as Data;



class VehiclesWaypoint extends Model
{
    protected $table = ID::Table;

	public $timestamps = Data::UseLaravelTimestamps;

	protected $fillable	= Data::FillableElements;		
	protected $hidden	= Data::HiddenElements;
	


	public function route ()
	{
		return $this->hasOne(VehiclesRoute::class);
	}

	///   Помогает сгенерировать часть чистого SQL запроса для Spatial-точки. 
	///____________________________________________________________________________________________
	///   
	///   Пример SQL Spatial точки для московского кремля: 
	///      GeomFromText('POINT(37.617654 55.751234)') 
	///   
	///   SRID (SpatialID) это идентификатор для указания систем исчисления географических данных. 
	///   В данном случае, я использую один из стандартных для примера, так как PHPMyAdmin 
	///   не любит нулевое значение, а с указанным индетификтором показывает точку на карте 
	///   в своем интерфейсе через OpenStreetMap (www.openstreetmap.org) 
	///   
	public static function MakePoint (float $XLng, float $YLat, $SpatialID = 4326)
	{
		return DB::raw(sprintf("ST_GeomFromText('POINT(%.6f %.6f)',%u)", $XLng, $YLat, $SpatialID));
	}
}



