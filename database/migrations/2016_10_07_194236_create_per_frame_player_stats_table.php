<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePerFramePlayerStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_frame_player_stats', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->string('api_game_id_long'); //hashed version
            $table->integer('api_game_id');  //int version
            $table->integer('api_match_player_id');
            $table->integer('x_position');
            $table->integer('y_position');
            $table->integer('current_gold');
            $table->integer('total_gold');
            $table->integer('level');
            $table->integer('xp');
            $table->integer('minions_killed');
            $table->integer('jungle_minions_killed');
            $table->integer('dominion_score');
            $table->integer('team_score');
            $table->integer('game_time_stamp');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_frame_player_stats');

    }
}
