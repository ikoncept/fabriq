<?php

namespace Ikoncept\Fabriq\Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call([
            PageTemplateSeeder::class,
            LocaleSeeder::class,
            // RoleSeeder::class
        ]);
    }
}
