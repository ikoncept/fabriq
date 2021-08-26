<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddHiddenToRolesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->boolean('hidden')->after('guard_name')->default(0);
        });
        DB::table('roles')->insert([
            'name'         => 'dev',
            'description'  => 'Developers',
            'display_name' => 'Dev',
            'guard_name'   => 'web',
            'hidden'       => true,
            'created_at'   => now(),
            'updated_at'   => now()
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('hidden');
        });

        DB::table('roles')->where('name', 'dev')->delete();
    }
}
