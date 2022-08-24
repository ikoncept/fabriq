<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTranslatableRevisionTemplatesTable extends Migration
{
    public function up()
    {
        $tableName = config('translatable-revisions.revision_templates_table_name');

        Schema::create($tableName, function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('slug');
            $table->nullableTimestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists(config('translatable-revisions.revision_templates_table_name'));
    }
}
