<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_stats', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index();
            $table->integer('bets_won')->default(0);
            $table->integer('bets_lost')->default(0);
            $table->integer('bets_complete')->default(0);
            $table->integer('weekly_streak')->default(0);
            $table->integer('monthly_streak')->default(0);
            $table->integer('alltime_streak')->default(0);
            $table->integer('weekly_wins')->default(0);
            $table->integer('montly_wins')->default(0);
            $table->integer('alltime_wins')->default(0);
            $table->boolean('redis_update')->default(false);
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
        Schema::dropIfExists('user_stats');
    }
}
