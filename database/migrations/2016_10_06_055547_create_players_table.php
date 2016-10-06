<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePlayersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('players', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('api_id');
            $table->string('slug');
            $table->string('name');
            $table->string('first_name');
            $table->string('last_name');
            $table->string('role_slug');
            $table->string('photo_url');
            $table->string('hometown')->nullable();
            $table->datetime('api_created_at');
            $table->datetime('api_updated_at');
            $table->integer('drupal_id');
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
        Schema::dropIfExists('players');
    }
}
