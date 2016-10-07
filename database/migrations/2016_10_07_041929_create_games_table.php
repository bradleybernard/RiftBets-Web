<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_match_id');
            $table->string('api_id_long');
            $table->string('name');
            $table->string('generated_name');
            $table->bigInteger('game_id')->nullable();
            $table->string('game_realm')->nullable();
            $table->string('platform_id')->nullable();
            $table->integer('revision');
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
        Schema::dropIfExists('games');
    }
}
