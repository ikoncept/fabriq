<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNestedSetFieldsToPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pages', function (Blueprint $table) {
            $table->unsignedInteger('sortindex')->after('id')->nullable();
            $table->unsignedInteger('parent_id')->after('id')->nullable();
            $table->unsignedInteger('_rgt')->after('id')->default(0);
            $table->unsignedInteger('_lft')->after('id')->default(0);

            $table->index([
                '_lft', '_rgt', 'parent_id',
            ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pages', function (Blueprint $table) {
            //
        });
    }
}
