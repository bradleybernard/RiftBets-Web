<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id')->unique();
            $table->string('slug');
            $table->string('name');
            $table->string('team_photo_url')->nullable();
            $table->string('logo_url')->nullable();
            $table->string('acronym')->nullable();
            $table->string('alt_logo_url')->nullable();
            $table->integer('drupalId')->nullable();
            $table->datetime('api_created_at');
            $table->datetime('api_updated_at');
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
        Schema::dropIfExists('teams');
    }
}
