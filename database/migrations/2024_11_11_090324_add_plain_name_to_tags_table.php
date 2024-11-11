<?php

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->string('plain_name')->after('type')->nullable();
        });
        Fabriq::getFqnModel('tag')::all()
            ->each(function ($item) {
                $item->plain_name = $item->name;
                $item->save();
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tags', function (Blueprint $table) {
            $table->dropColumn('plain_name');
        });
    }
};
