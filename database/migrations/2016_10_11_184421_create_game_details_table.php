<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_details', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->integer('event_id');
            $table->string('key');
            $table->string('value');
            // $table->integer('api_match_player_id')->nullable();
            // $table->string('level_up_type')->nullable();
            // $table->string('ward_type')->nullable();
            // $table->integer('killed_id')->nullable();
            // $table->integer('creator_id')->nullable();
            // $table->integer('x_position')->nullable();
            // $table->integer('y_position')->nullable();
            // $table->integer('team_id')->nullable();
            // $table->string('building_type')->nullable();
            // $table->string('lane_type')->nullable();
            // $table->string('tower_type')->nullable();
            // $table->integer('victim_id')->nullable();
            // $table->integer('assisting_player_id')->nullable();
            // $table->string('monster_type')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_details');
    }
}
