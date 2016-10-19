<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_details', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('bet_id');
            $table->integer('game_id');
            $table->integer('question_id');
            $table->integer('answer_id')->nullable();
            $table->string('user_answer');
            $table->integer('credits_placed')->unsigned();
            $table->integer('credits_won')->default(0);
            $table->boolean('is_complete')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bet_details');
    }
}
