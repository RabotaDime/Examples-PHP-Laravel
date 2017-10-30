<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\My\WaypointsAPI\ID		\VehiclesRoute as ID;
use App\My\WaypointsAPI\Data	\VehiclesRoute as Data;



class VehiclesRoute extends Model
{
    protected $table = ID::Table;

	public $timestamps = Data::UseLaravelTimestamps;

	protected $fillable	= Data::FillableElements;		
	protected $hidden	= Data::HiddenElements;
	


	public function waypoints ()
	{
		return $this->hasMany(VehiclesWaypoint::class);
	}
}

