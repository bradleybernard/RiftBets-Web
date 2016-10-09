<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBracketRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bracket_records', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_tournament_id');
            $table->string('api_bracket_id');
            $table->string('api_roster_id');
            $table->integer('wins');
            $table->integer('losses');
            $table->integer('ties');
            $table->integer('score');
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
        Schema::dropIfExists('bracket_records');
    }
}
