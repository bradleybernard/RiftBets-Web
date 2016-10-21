<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDdragonSummonersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ddragon_summoners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_version');
            $table->integer('api_id');
            $table->string('key');
            $table->string('name');
            $table->string('description');
            $table->string('image_full');
            $table->string('image_group');
            $table->string('image_url');
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
        Schema::dropIfExists('ddragon_summoners');
    }
}
