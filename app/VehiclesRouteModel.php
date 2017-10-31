<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\My\WaypointsAPI\Data	\VehiclesRoute as Data;



class VehiclesRoute extends Model
{
    protected $table = Data::Table;

	public $timestamps = Data::UseLaravelTimestamps;

	protected $fillable	= Data::FillableElements;		
	protected $hidden	= Data::HiddenElements;
	


	public function waypoints ()
	{
		return $this->hasMany(VehiclesWaypoint::class);
	}
}

