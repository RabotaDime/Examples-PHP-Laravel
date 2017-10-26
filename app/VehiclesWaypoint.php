<?php

namespace App;

use Illuminate\Database\Eloquent\Model;



class VehiclesWaypoint extends Model
{
	///  Хочу, чтобы все таблицы, связанные с данными API, были проименованы
	///  с моим префиксом. 
    protected $table = 'api_vehicleswaypoints';

	///  Временные метки для этой модели данных не нужны. У меня будут свои.
	public $timestamps = false;
}

