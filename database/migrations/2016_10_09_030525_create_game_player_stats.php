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
            $table->integer('participant_id');
            $table->integer('team_id');
            $table->integer('champion_id');
            $table->integer('spell1_id');
            $table->integer('spell2_id');
            $table->integer('item_1');
            $table->integer('item_2');
            $table->integer('item_3');
            $table->integer('item_4');
            $table->integer('item_5');
            $table->integer('item_6');
            $table->integer('kills');
            $table->integer('deaths');
            $table->integer('assists');
            $table->integer('gold_earned');
            $table->integer('minions_killed');
            $table->integer('champ_level');
            $table->string('summoner_name');
            $table->integer('profile_icon');
            $table->timestamp('created_at');
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
