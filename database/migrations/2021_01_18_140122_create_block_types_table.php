<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBlockTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('block_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('component_name');
            $table->boolean('active')->default(0);
            $table->string('type')->nullable();
            $table->boolean('has_children')->default(0);
            $table->text('description')->nullable();
            $table->text('base_64_svg')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('block_types');
    }
}
