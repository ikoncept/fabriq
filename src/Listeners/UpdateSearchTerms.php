<?php

namespace Ikoncept\Fabriq\Listeners;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Helpers\RecursiveArrayValues;
use Ikoncept\Fabriq\Models\SearchTerm;
use Infab\TranslatableRevisions\Events\DefinitionsPublished;
use Infab\TranslatableRevisions\Events\DefinitionsUpdated;

class UpdateSearchTerms
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct() {}

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DefinitionsUpdated|DefinitionsPublished $event)
    {
        $options = $event->model->getRevisionOptions();

        if (! $options->isIndexable) {
            return;
        }

        if ($options->indexFunction) {
            call_user_func_array($options->indexFunction, [
                'args' => [
                    'definitions' => $event->definitions,
                ],
            ]);

            return;
        }

        if (get_class($event) === DefinitionsUpdated::class && Fabriq::getFqnModel('page') === get_class($event->model)) {

            return;
        }

        $event->model->paths->each(function ($path) use ($event, $options) {
            $locale = collect($path)->keys()->first();

            if (! isset($event->definitions[$locale][$options->titleKey])) {
                return;
            }

            $indexedKeys = RecursiveArrayValues::fromCollection($event->definitions[$locale], $options->indexableKeys);
            $title = $event->definitions[$locale][$options->titleKey];

            $data = [
                'model_id' => $event->model->id,
                'model_type' => get_class($event->model),
                'title' => $title,
                'locale' => $locale,
                'path' => collect($path)->flatten()->first(),
                'search_string' => implode(' ', $indexedKeys),
            ];

            SearchTerm::updateOrCreate([
                'model_id' => $data['model_id'],
                'model_type' => $data['model_type'],
                'locale' => $data['locale'],
            ], $data);
        });
    }
}
