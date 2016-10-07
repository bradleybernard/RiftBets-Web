<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBreakpointResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('breakpoint_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->string('api_breakpoint_id');
            $table->string('api_resource_id');
            $table->string('resource_type');
            $table->integer('standing')->nullable();
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
        Schema::dropIfExists('breakpoint_resources');
    }
}
