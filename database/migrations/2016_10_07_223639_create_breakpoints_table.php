<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakpointsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breakpoints', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_tournament_id');
            $table->string('api_id_long');
            $table->string('name');
            $table->integer('position');
            $table->string('generator_identifier');
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
        Schema::dropIfExists('breakpoints');
    }
}
