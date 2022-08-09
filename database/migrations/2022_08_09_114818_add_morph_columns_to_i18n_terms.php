<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasColumn('i18n_terms', 'model_type')) {
            return;
        }

        Schema::table('i18n_terms', function (Blueprint $table) {
            $table->nullableMorphs('model', 'term_model');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (! Schema::hasColumn('i18n_terms', 'model_type')) {
            return;
        }
        Schema::table('i18n_terms', function (Blueprint $table) {
            $table->dropMorphs('model', 'term_model');
        });
    }
};
