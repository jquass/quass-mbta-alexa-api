<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateVocalizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vocalizations', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('stop_id')->unsigned()->nullable();
            $table->bigInteger('direction_id')->unsigned()->nullable();
            $table->bigInteger('route_id')->unsigned()->nullable();
            $table->string('handle');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('vocalizations');
    }
}
