<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVehiclesWaypointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('api_vehicleswaypoints', function (Blueprint $table) {

			///  В данном примере я рассматриваю, что это поле не нужно. 
			///  Но из-за особенностей работы баз данных и теоретической необходимости 
			///  в будущем как-то манипулировать точками пути, я просто сделаю это поле
			///  большим, раз точек будет очень много. Манипуляция с точками может 
			///  потребоваться, например, для удаления нескольких ошибочных точек,
			///  которые выбиваются из пути, например, из-за сбоя оборудования. 
            $table->bigIncrements	('ID');

			$table->integer			('VehicleID')->unsigned();	/// FKEY!
			$table->dateTime		('Time');
			$table->float			('Speed', 8, 3);
			$table->point			('Coord');

			$table->foreign			('VehicleID')->references('ID')->on('api_vehicles'); //->onDelete('cascade');

			///  TODO: На данный момент не работал с этим типом, 
			///  плюс встает вопрос совместимости версий. 
			///  Надо попробовать разные варианты. 
			//$table->point('Coord');
			///  Широта, (f)Latitude   -90.######  +90.######
			//$table->decimal('CoordLat', 10, 8);
			///  Долгота, Longitude   -180.###### +180.###### 
			//$table->decimal('CoordLng', 11, 8);

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
        Schema::dropIfExists('api_vehicleswaypoints');
    }
}

