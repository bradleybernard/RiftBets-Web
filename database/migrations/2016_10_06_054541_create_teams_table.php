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
            $table->integer('api_id');
            $table->string('slug');
            $table->string('name');
            $table->string('team_photo_url');
            $table->string('logo_url');
            $table->string('acronym');
            $table->string('alt_logo_url')->nullable();
            $table->datetime('api_created_at');
            $table->datetime('api_updated_at');
            $table->integer('drupalId');
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
