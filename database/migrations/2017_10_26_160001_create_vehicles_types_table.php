<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_vehicletypes', function (Blueprint $table) {

            $table->increments	('ID');

			$table->string		('ModelName');
			$table->string		('BrandName');
			$table->string		('Description');
			$table->integer		('VehicleClass');
			$table->integer		('EngineVolume');
			$table->integer		('ServicePeriod');
			$table->float		('MaxSpeed', 8, 3);

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
        Schema::dropIfExists('api_vehicletypes');
    }
}

