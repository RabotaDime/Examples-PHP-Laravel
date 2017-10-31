<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\My\WaypointsAPI\Data	\VehiclesWaypoint as Data;



class VehiclesWaypoint extends Model
{
    protected $table = Data::Table;

	public $timestamps = Data::UseLaravelTimestamps;

	protected $fillable	= Data::FillableElements;		
	protected $hidden	= Data::HiddenElements;
	


	public function route ()
	{
		return $this->hasOne(VehiclesRoute::class);
	}
}



