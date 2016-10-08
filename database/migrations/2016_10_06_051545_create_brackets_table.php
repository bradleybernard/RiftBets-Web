<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBracketsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('brackets', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_tournament_id'); //api_id or tournaments.id hmm
            $table->string('api_id_long')->unique();
            $table->string('name')->nullable();
            $table->integer('position');
            $table->integer('group_position');
            $table->string('group_name')->nullable();
            $table->boolean('can_manufacture');
            $table->string('state');
            $table->string('game_identifier')->nullable();
            $table->integer('game_required_players')->nullable();
            $table->string('game_map_name')->nullable();
            $table->integer('game_required_teams')->nullable();
            $table->string('bracket_identifier')->nullable();
            $table->integer('bracket_rounds')->nullable();
            $table->string('match_identifier')->nullable();
            $table->integer('match_best_of')->nullable();
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
        Schema::dropIfExists('brackets');
    }
}
