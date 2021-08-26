<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateImageablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('imageables', function (Blueprint $table) {
            $table->id('id');
            $table->unsignedBigInteger('image_id')->index();
            $table->foreign('image_id')->references('id')->on('images')->onDelete('cascade');
            $table->unsignedBigInteger('imageable_id');
            $table->string('imageable_type');
            $table->index(['imageable_type', 'imageable_id']);
            $table->unsignedInteger('sortindex')->default(0)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('imageables');
    }
}
