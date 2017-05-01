<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateStopsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('stops', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('route_id')->unsigned();
            $table->bigInteger('direction_id')->unsigned();
            $table->integer('mbta_stop_order');
            $table->string('mbta_stop_id');
            $table->string('mbta_stop_name');
            $table->string('mbta_parent_station');
            $table->string('mbta_parent_station_name');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('stops');
    }
}
