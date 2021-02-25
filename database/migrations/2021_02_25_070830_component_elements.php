<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ComponentElements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('component_elements', function (Blueprint $table) {
            $table->id();
            $table->float('count');
            $table->foreignId('base_component_id')->constrained('components');
            $table->foreignId('component_id')->constrained('components');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('component_elements');
    }
}
