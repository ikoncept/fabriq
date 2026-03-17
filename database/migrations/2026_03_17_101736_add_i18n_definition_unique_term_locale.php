<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddI18nDefinitionUniqueTermLocale extends Migration
{
    public function up()
    {
        $prefix = config('translatable-revisions.i18n_table_prefix_name');

        Schema::table($prefix.'i18n_definitions', function (Blueprint $table) {
            $table->unique(['term_id', 'locale']);
        });
    }

    public function down()
    {
        $prefix = config('translatable-revisions.i18n_table_prefix_name');

        Schema::table($prefix.'i18n_definitions', function (Blueprint $table) {
            $table->dropUnique(['term_id', 'locale']);
            $table->index(['term_id', 'locale']);
        });
    }
}
