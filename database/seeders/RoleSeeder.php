<?php

namespace Ikoncept\Fabriq\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $adminRole = DB::table('roles')->insert([
            'id'           => 1,
            'name'         => 'admin',
            'description'  => 'Administratörer',
            'display_name' => 'Admin',
            'guard_name'   => 'web',
            'created_at'   => now(),
            'updated_at'   => now()
        ]);
    }
}
