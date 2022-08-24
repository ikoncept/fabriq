<?php

namespace Ikoncept\Fabriq\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LocaleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $svLocale = DB::table(config('page-module.i18n_table_prefix_name').'i18n_locales')->insert([
            'name' => 'Swedish',
            'native' => 'Svenska',
            'regional' => 'se_SV',
            'iso_code' => 'sv',
            'enabled' => true,
        ]);
    }
}
