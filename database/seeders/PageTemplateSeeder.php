<?php

namespace Ikoncept\Fabriq\Database\Seeders;

use Ikoncept\Fabriq\Models\BlockType;
use Illuminate\Database\Seeder;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

class PageTemplateSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $template = RevisionTemplate::factory()->create([
            'name' => 'Landningssida',
            'slug' => 'startsida'
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Meta-titel',
            'key' => 'meta_title',
            'group' => 'meta',
            'options' => ['classes' => 'col-span-5'],
            'type' => 'text',
            'sort_index' => 10,
            'translated' => true
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Meta-beskrivning',
            'key' => 'meta_description',
            'options' => ['classes' => 'col-span-5'],
            'group' => 'meta',
            'type' => 'text',
            'sort_index' => 20,
            'translated' => true
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Meta-bild',
            'key' => 'meta_og_image',
            'group' => 'meta',
            'options' => ['group' => 'meta_og_image', 'classes' => 'col-span-4'],
            'type' => 'image',
            'sort_index' => 30,
            'translated' => true
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Titel',
            'key' => 'page_title',
            'group' => 'main_content',
            'options' => ['classes' => 'hidden'],
            'type' => 'text',
            'sort_index' => 40,
            'translated' => true
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Rubrik',
            'key' => 'page_header',
            'group' => 'main_content',
            'options' => ['classes' => 'col-span-5'],
            'type' => 'text',
            'sort_index' => 40,
            'translated' => true
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Huvudbild',
            'key' => 'main_image',
            'group' => 'main_content',
            'options' => ['group' => 'main_image', 'classes' => 'col-span-4'],
            'type' => 'image',
            'sort_index' => 50,
            'translated' => true
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Innehåll',
            'key' => 'page_content',
            'group' => 'main_content',
            'type' => 'html',
            'sort_index' => 60,
            'translated' => true
        ]);
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'translated' => true,
            'name' => 'Box',
            'key' => 'boxes',
            'repeater' => true,
            'group' => 'boxes',
            'type' => 'repeater',
            'sort_index' => 60,
        ]);

        BlockType::factory()->create([
            'name' => 'Demo-block',
            'component_name' => 'DemoBlock',
            'active' => 1,
            'type' => 'block',
            'has_children' => 1
        ]);
    }
}
