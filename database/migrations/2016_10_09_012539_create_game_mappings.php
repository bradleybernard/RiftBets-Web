<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameMappings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_mappings', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_match_id');
            $table->string('api_game_id')->unique();
            $table->integer('game_id');
            $table->string('game_hash');
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
        Schema::drop('game_mappings');
    }
}
