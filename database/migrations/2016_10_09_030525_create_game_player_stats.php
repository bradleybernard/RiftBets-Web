<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePlayerStats extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_player_stats', function (Blueprint $table) 
        {
            $table->increments('id');
            $table->integer('game_id');
            $table->integer('participant_id');
            $table->integer('team_id');
            $table->integer('champion_id');
            $table->integer('spell1_id');
            $table->integer('spell2_id');
            $table->integer('item_1')->nullable();
            $table->integer('item_2')->nullable();
            $table->integer('item_3')->nullable();
            $table->integer('item_4')->nullable();
            $table->integer('item_5')->nullable();
            $table->integer('item_6')->nullable();
            $table->integer('kills');
            $table->integer('deaths');
            $table->integer('assists');
            $table->integer('gold_earned');
            $table->integer('minions_killed');
            $table->integer('champ_level');
            $table->string('summoner_name');
            $table->integer('profile_icon');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_player_stats');
    }
}
