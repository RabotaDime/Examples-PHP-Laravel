<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VehiclesType extends Model
{
	///  Хочу, чтобы все таблицы, связанные с данными API, были проименованы
	///  с моим префиксом. 
    protected $table = 'api_vehicletypes';

	///  Временные метки для этой модели данных не нужны. У меня будут свои.
	public $timestamps = false;
}
