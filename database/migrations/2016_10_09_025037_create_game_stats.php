<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_stats', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->integer('game_id'); //hashed version
            $table->string('platform_id');  //int version
            $table->bigInteger('game_creation');
            $table->integer('game_duration');
            $table->integer('queue_id');
            $table->integer('map_id');
            $table->integer('season_id');
            $table->string('game_version');
            $table->string('game_mode');
            $table->string('game_type');
            $table->datetime('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_stats');
    }
}
