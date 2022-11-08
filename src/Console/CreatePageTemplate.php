<?php

namespace Ikoncept\Fabriq\Console;

use Ikoncept\Fabriq\Actions\ClonePage;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Console\Command;
use Illuminate\Support\Str;
use Infab\TranslatableRevisions\Models\RevisionTemplate;

class CreatePageTemplate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:create-page-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new page template';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(ClonePage $clonePage)
    {
        $this->info('Create page template');

        $sourceId = (int) $this->ask('What ID should this template be based on?');
        $sourcePage = Fabriq::getModelClass('page')->whereId($sourceId)->firstOrFail();

        $this->info('Page found: '.$sourcePage->name);
        $continue = $this->confirm('Continue?');

        if (! $continue) {
            $this->warn('Exiting');

            return 0;
        }

        $templateName = $this->ask('What should the template be named?');
        $lockedTemplate = $this->confirm('Should the template be locked?');

        $sourceTemplate = RevisionTemplate::where('slug', 'startsida')
            ->with('fields')
            ->first();

        if (! $sourceTemplate) {
            $this->error('No source template found, searched for "startsida", exiting');

            return 1;
        }

        // Figure out hidden root
        Fabriq::getFqnModel('page')::unguard();
        $root = Fabriq::getFqnModel('page')::firstOrCreate([
            'name' => 'hidden_root',
        ], [
            'name' => 'hidden_root',
            'sortindex' => 0,
            'parent_id' => null,
            'revision' => 1,
            'template_id' => $sourceTemplate->id,
        ]);
        Fabriq::getFqnModel('page')::reguard();

        RevisionTemplate::unguard();
        $newTemplate = RevisionTemplate::create([
            'name' => $templateName,
            'slug' => Str::slug($templateName),
            'type' => 'page',
            'locked' => $lockedTemplate,
        ]);
        foreach ($sourceTemplate->fields as $field) {
            $newField = $field->replicate();
            $newField->template_id = $newTemplate->id;
            $newField->save();
        }

        RevisionTemplate::reguard();

        $clonedPage = $clonePage($root, $sourcePage, $templateName);
        $clonedPage->locked = $lockedTemplate;
        $clonedPage->template_id = $newTemplate->id;
        $clonedPage->save();

        $newTemplate->source_model_id = $clonedPage->id;
        $newTemplate->save();

        $this->info('Cloned page into hidden tree');

        return 0;
    }
}
