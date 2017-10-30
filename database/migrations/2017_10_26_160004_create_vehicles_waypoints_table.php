<?php

//use Illuminate\Support\Facades\Schema;
//use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

use App\My\WaypointsAPI\Data\VehiclesWaypoint as Data;



class CreateVehiclesWaypointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Data::ExecuteMigration();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Data::ReverseMigration();
    }
}



