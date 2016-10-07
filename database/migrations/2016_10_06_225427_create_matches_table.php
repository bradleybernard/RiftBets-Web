<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('matches', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_bracket_id');
            $table->string('api_id_long');
            $table->string('name');
            $table->integer('position');
            $table->string('state');
            $table->integer('group_position');
            $table->string('scoring_identifier')->nullable();
            $table->string('resource_type')->nullable();
            $table->string('api_resource_id_one')->nullable();
            $table->string('api_resource_id_two')->nullable();
            $table->integer('score_one')->nullable();
            $table->integer('score_two')->nullable();
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
        Schema::dropIfExists('matches');
    }
}
