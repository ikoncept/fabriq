<?php

use Ikoncept\Fabriq\Database\Seeders\PageTemplateSeeder;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddLandingPageTemplateToTemplatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if(!DB::table('revision_templates')->where('type', 'page')->first()) {
            Artisan::call('db:seed', [
                '--class' => 'Ikoncept\Fabriq\Database\Seeders\PageTemplateSeeder',
                '--force' => true
            ]);
        }
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
