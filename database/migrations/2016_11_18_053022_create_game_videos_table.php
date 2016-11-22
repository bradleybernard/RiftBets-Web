<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_videos', function (Blueprint $table) {
            $table->increments('id');

            $table->integer('api_id');
            $table->string('api_game_id');
            $table->string('locale');
            $table->string('source');

            $table->datetime('api_created_at');
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
        Schema::dropIfExists('game_videos');
    }
}
