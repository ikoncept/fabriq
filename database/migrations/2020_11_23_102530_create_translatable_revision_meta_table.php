<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTranslatableRevisionMetaTable extends Migration
{
    public function up()
    {
        $tableName = config('translatable-revisions.revision_meta_table_name');

        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('model', 'model');
            // $table->unsignedBigInteger('model_id');
            // $table->foreign('model_id')->references('id')->on(config('translatable-revisions.revisions_table_name'))->onDelete('cascade');
            $table->integer('model_version')->nullable();
            $table->string('meta_key');
            $table->text('meta_value')->nullable();
            $table->nullableTimestamps();
        });
    }
}
