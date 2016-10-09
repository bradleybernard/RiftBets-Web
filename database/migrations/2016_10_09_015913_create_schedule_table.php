<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScheduleTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedule', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_league_id');
            $table->string('api_id_long');
            $table->string('api_tournament_id');
            $table->string('api_match_id')->nullable();
            $table->string('block_prefix')->nullable();
            $table->string('block_label')->nullable();
            $table->string('sub_block_prefix')->nullable();
            $table->string('sub_block_label')->nullable();
            $table->datetime('scheduled_time');
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
        Schema::dropIfExists('schedule');
    }
}
