<?php

namespace Ikoncept\Fabriq\Console;

use Illuminate\Console\Command;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Infab\TranslatableRevisions\Models\RevisionTemplateField;

class MakeRevisionField extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:make-revision-field';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generates a revision field';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // $name = $this->ask("Which ?") ?: '';
        $templateOptions = RevisionTemplate::select('id','name', 'slug')
            ->get();

        $choices = $templateOptions->map(function($item) {
            return $item->name . ' (' . $item->slug . ') | id: ' . $item->id;
        });

        $template = $this->choice('Which main template do you want to attach the template field to?', $choices->toArray());
        if(! preg_match_all('/id: ([\d]+)/', $template, $matches)) {
            $this->error('Failed to extract id, exiting');
            return 1;
        }

        $ff = RevisionTemplateField::where('template_id', $matches[1][0])->select('id')->get()->count();
        $sortindex = ($ff + 1) * 10;
        // dd( + 1 * 10);
        $fieldName = $this->ask('Enter a name for the field');
        $fieldKey =  $this->ask('Enter a key for the field');
        $translated =  $this->choice('Is the field translated?', ['Yes', 'No'], 0);
        $repeater =  $this->choice('Is the field a repeater?', ['Yes', 'No'], 1);
        $fieldType =  $this->choice('Choose field type', ['text', 'textarea', 'image', 'html', 'date_time', 'video', 'date'], 0);


        $newField = new RevisionTemplateField();
        $newField->template_id = $matches[1][0];
        $newField->name = $fieldName;
        $newField->key = $fieldKey;
        $newField->translated = ($translated === 'Yes') ? true : false;
        $newField->repeater = ($repeater === 'Yes') ? true : false;
        $newField->type = $fieldType;
        $newField->sort_index = RevisionTemplate::where('id', $matches[1][0])->withCount('fields')->first()->fieldsCount + 1 * 10;
        $newField->sort_index = $sortindex;
        $newField->save();


        $this->info('Template field created successfully!');

        return 0;
    }
}
