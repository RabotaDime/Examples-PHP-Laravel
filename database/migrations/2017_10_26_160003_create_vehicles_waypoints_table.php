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

			///  � ������ ������� � ������������, ��� ��� ���� �� �����. 
			///  �� ��-�� ������������ ������ ��� ������ � ������������� ������������� 
			///  � ������� ���-�� �������������� ������� ����, � ������ ������ ��� ����
			///  �������, ��� ����� ����� ����� �����. ����������� � ������� ����� 
			///  �������������, ��������, ��� �������� ���������� ��������� �����,
			///  ������� ���������� �� ����, ��������, ��-�� ���� ������������. 
            $table->bigIncrements	('ID');

			$table->integer			('VehicleID')->unsigned();	/// FKEY!
			$table->dateTime		('Time');
			$table->float			('Speed', 8, 3);
			$table->point			('Coord');

			$table->foreign			('VehicleID')->references('ID')->on('api_vehicles'); //->onDelete('cascade');

			///  TODO: �� ������ ������ �� ������� � ���� �����, 
			///  ���� ������ ������ ������������� ������. 
			///  ���� ����������� ������ ��������. 
			//$table->point('Coord');
			///  ������, (f)Latitude   -90.######  +90.######
			//$table->decimal('CoordLat', 10, 8);
			///  �������, Longitude   -180.###### +180.###### 
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

