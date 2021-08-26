<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('type')->default('internal')->after('title')->nullable();
        });
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('i18n_key');
        });
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('title');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('menu_items', function (Blueprint $table) {
            $table->dropColumn('type');
        });
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('i18n_key')->nullable()->after('id');
        });
        Schema::table('menu_items', function (Blueprint $table) {
            $table->string('title')->nullable()->after('id');
        });
    }
}
