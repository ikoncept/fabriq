<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUnpublishesAtFieldsToArticlesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->boolean('has_unpublished_time')->after('publishes_at')->default(0);
            $table->dateTime('unpublishes_at')->after('publishes_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('has_unpublished_time');
        });
        Schema::table('articles', function (Blueprint $table) {
            $table->dropColumn('unpublishes_at');
        });
    }
}
