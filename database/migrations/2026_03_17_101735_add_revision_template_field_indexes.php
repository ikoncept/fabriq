<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisionTemplateFieldIndexes extends Migration
{
    public function up()
    {
        $tableName = config('translatable-revisions.revision_template_fields_table_name');

        Schema::table($tableName, function (Blueprint $table) {
            $table->index(['template_id', 'key'], 'revision_template_fields_lookup_idx');
        });
    }

    public function down()
    {
        $tableName = config('translatable-revisions.revision_template_fields_table_name');

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropIndex('revision_template_fields_lookup_idx');
        });
    }
}
