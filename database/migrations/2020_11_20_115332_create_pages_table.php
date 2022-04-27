<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePagesTable extends Migration
{
    public function up()
    {
        // $tableName = config('page-module.pages_table_name');

        Schema::create('pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->integer('published_version')->nullable();
            $table->integer('revision')->default(1);
            $table->unsignedBigInteger('template_id');
            $table->timestamp('published_at')->nullable();
            $table->nullableTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('pages');
    }
}
