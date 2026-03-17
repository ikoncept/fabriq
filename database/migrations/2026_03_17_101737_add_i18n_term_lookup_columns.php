<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddI18nTermLookupColumns extends Migration
{
    public function up()
    {
        $prefix = config('translatable-revisions.i18n_table_prefix_name');
        $tableName = $prefix.'i18n_terms';

        Schema::table($tableName, function (Blueprint $table) {
            $table->integer('model_version')->nullable()->after('model_id');
            $table->string('field_key')->nullable()->after('model_version');
            $table->index(['model_type', 'model_id', 'model_version', 'field_key'], 'i18n_terms_lookup_idx');
        });
    }

    public function down()
    {
        $prefix = config('translatable-revisions.i18n_table_prefix_name');
        $tableName = $prefix.'i18n_terms';

        Schema::table($tableName, function (Blueprint $table) {
            $table->dropIndex('i18n_terms_lookup_idx');
            $table->dropColumn('field_key');
            $table->dropColumn('model_version');
        });
    }
}
