<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

class CreateSmartBlocksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('smart_blocks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->timestamps();
        });

        $template = RevisionTemplate::factory()->create([
            'name' => 'Smart Block',
            'slug' => 'smart_block',
            'type' => 'smart_block',
        ]);

        RevisionTemplateField::factory()->create([
            'template_id' => $template->id,
            'translated' => true,
            'name' => 'Box',
            'key' => 'boxes',
            'repeater' => true,
            'group' => 'boxes',
            'type' => 'repeater',
            'sort_index' => 10,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('smart_blocks');
    }
}
