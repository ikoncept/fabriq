<?php

use Illuminate\Database\Migrations\Migration;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

class AddArticleRevisionTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        $template = RevisionTemplate::factory()->create([
            'name' => 'Nyhet',
            'slug' => 'article',
            'type' => 'article',
        ]);
        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Titel',
            'key' => 'title',
            'type' => 'text',
            'translated' => true,
            'group' => 'article',
            'repeater' => false,
            'sort_index' => 10,
        ]);
        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'image',
            'key' => 'image',
            'type' => 'image',
            'translated' => false,
            'group' => 'article',
            'repeater' => false,
            'sort_index' => 20,
        ]);

        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Ingress',
            'key' => 'preamble',
            'type' => 'textarea',
            'translated' => true,
            'group' => 'article',
            'repeater' => false,
            'sort_index' => 30,
        ]);

        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Innehåll',
            'key' => 'body',
            'type' => 'html',
            'translated' => true,
            'group' => 'article',
            'repeater' => false,
            'sort_index' => 40,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
