<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDdragonChampionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ddragon_champions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_version');
            $table->integer('api_id');
            $table->string('champion_id');
            $table->string('champion_name');
            $table->string('champion_title');
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
        Schema::dropIfExists('ddragon_champions');
    }
}
