<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateI18nTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $prefix = config('translatable-revisions.i18n_table_prefix_name');

        Schema::create($prefix . 'i18n_locales', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('name');
            $table->string('native');
            $table->string('regional');
            $table->string('iso_code');
            $table->boolean('enabled')->default(0);
            $table->integer('sort_index')->default(0);
        });

        Schema::create($prefix . 'i18n_terms', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key')->unique();
            $table->string('description');
            $table->string('group')->default('ungrouped');
            $table->timestamps();
            $table->index('key');
        });

        Schema::create($prefix . 'i18n_definitions', function (Blueprint $table) use ($prefix) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('term_id');
            $table->foreign('term_id')->references('id')->on($prefix . 'i18n_terms')->onDelete('cascade');
            $table->string('locale');
            $table->longText('content')->nullable();
            $table->boolean('approved')->default(0);
            $table->timestamps();

            $table->index(['term_id','locale']);
        });

        DB::table($prefix . 'i18n_locales')->insert([
            'name' => 'English',
            'native' => 'English',
            'iso_code' => 'en',
            'regional' => 'en_GB',
            'enabled' => true
        ]);

        DB::table($prefix . 'i18n_locales')->insert([
            'name' => 'Swedish',
            'native' => 'Svensk',
            'iso_code' => 'sv',
            'regional' => 'sv_SE',
            'enabled' => true
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
