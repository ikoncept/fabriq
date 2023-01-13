<?php

use Illuminate\Database\Migrations\Migration;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        RevisionTemplateField::unguard();
        $template = RevisionTemplate::where('slug', 'contact')->first();
        if (! $template) {
            return;
        }
        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Språk',
            'key' => 'enabled_locales',
            'group' => 'contact',
            'options' => [],
            'type' => 'text',
            'sort_index' => 40,
            'translated' => false,
        ]);
        RevisionTemplateField::reguard();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $field = RevisionTemplateField::where('key', 'enabled_locales')->first();
        if ($field) {
            $field->delete();
        }
    }
};
