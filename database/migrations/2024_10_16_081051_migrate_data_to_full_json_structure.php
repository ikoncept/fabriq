<?php

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Migrations\Migration;
use Infab\TranslatableRevisions\Models\I18nLocale;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $pages = Fabriq::getFqnModel('page')::all();
        foreach ($pages->reverse() as $page) {
            I18nLocale::whereEnabled(1)->get()->each(function ($locale) use ($page) {
                $revisionContent = $page->getFieldContent($page->revision, $locale->iso_code);
                $page->updateContent($revisionContent->toArray(), $locale->iso_code);
                $page->publish($page->revision, $locale->iso_code);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void {}
};
