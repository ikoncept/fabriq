<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Kalnoy\Nestedset\NestedSet;

class CreateMenuItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('menu_id');
            $table->foreign('menu_id')->references('id')->on('menus')->onDelete('cascade');
            // $table->nestedSet();
            NestedSet::columns($table);
            $table->string('title');
            $table->string('i18n_key')->nullable();
            $table->string('explicit_url')->nullable();
            $table->boolean('is_external')->default(0);
            $table->string('external_url')->nullable();
            $table->string('redirect_url')->nullable();
            $table->boolean('redirect')->default(0);
            $table->boolean('permanent_redirect')->default(0);
            $table->boolean('interactive')->default(0);
            $table->unsignedBigInteger('page_id')->nullable();
            $table->foreign('page_id')->references('id')->on('pages')->onDelete('set null');
            $table->unsignedInteger('sortindex')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('menu_items');
    }
}
