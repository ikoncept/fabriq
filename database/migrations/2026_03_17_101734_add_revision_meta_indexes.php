<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddRevisionMetaIndexes extends Migration
{
    public function up()
    {
        $tableName = config('translatable-revisions.revision_meta_table_name');

        Schema::table($tableName, function (Blueprint $table) {
            $table->index(['model_type', 'model_id', 'model_version'], 'revision_meta_lookup_idx');
            $table->unique(['model_type', 'model_id', 'model_version', 'meta_key'], 'revision_meta_unique_key');
        });
    }

    public function down()
    {
        $tableName = config('translatable-revisions.revision_meta_table_name');

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropUnique('revision_meta_unique_key');
            $table->dropIndex('revision_meta_lookup_idx');
        });
    }
}
