<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

class AddContactRevisionTemplate extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $template = RevisionTemplate::factory()->create([
            'name' => 'Kontakt',
            'slug' => 'contact',
            'type' => 'contact'
        ]);
        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Text',
            'key' => 'body',
            'type' => 'html',
            'translated' => true,
            'group' => 'contact',
            'repeater' => false,
            'sort_index' => 10
        ]);
        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'image',
            'key' => 'image',
            'type' => 'image',
            'translated' => false,
            'group' => 'contact',
            'repeater' => false,
            'sort_index' => 20
        ]);

        $templateField = RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'name' => 'Roll',
            'key' => 'position',
            'type' => 'text',
            'translated' => true,
            'group' => 'contact',
            'repeater' => false,
            'sort_index' => 30
        ]);

        Schema::table('contacts', function (Blueprint $table) {
            $table->dropColumn('body');
        });

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
