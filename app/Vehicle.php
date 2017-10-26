<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
	///  Хочу, чтобы все таблицы, связанные с данными API, были проименованы
	///  с моим префиксом. 
    protected $table = 'api_vehicles';

	///  Временные метки для этой модели данных не нужны. У меня будут свои.
	public $timestamps = false;
}
