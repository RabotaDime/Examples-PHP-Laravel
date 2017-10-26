<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_vehicles', function (Blueprint $table) {

            $table->increments	('ID');

			$table->integer		('TypeID')->unsigned();		///  FKEY! 
			$table->integer		('MileageInMin');			///  Пробег в минутах. 
			$table->string		('Description');			///  Дополнительное описание. 
			$table->datetime	('LastServiceTime');		///  Последнее обслуживание. 

			$table->foreign		('TypeID')->references('ID')->on('api_vehicletypes'); //->onDelete('cascade');

            //$table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('api_vehicles');
    }
}
