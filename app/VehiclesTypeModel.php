<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\My\WaypointsAPI\Data	\VehiclesType as Data;



class VehiclesType extends Model
{
    protected $table = Data::Table;

	public $timestamps = Data::UseLaravelTimestamps;

	protected $fillable	= Data::FillableElements;		
	protected $hidden	= Data::HiddenElements;



	public function vehicles ()
	{
		return $this->hasMany(Vehicle::class);
	}
}

