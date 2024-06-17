<?php

namespace Ikoncept\Fabriq\Listeners;

use Ikoncept\Fabriq\Models\SearchTerm;
use Illuminate\Support\Collection;
use Infab\TranslatableRevisions\Events\DefinitionsPublished;

class UpdateSearchTerms
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DefinitionsPublished $event)
    {
        if (! $event->model->getRevisionOptions()->isIndexable) {
            return;
        }

        $event->model->paths->each(function ($path) use ($event) {
            $locale = collect($path)->keys()->first();

            $indexableKeys = $this->array_value_recursive($event->definitions[$locale], $event->model->getRevisionOptions()->indexableKeys);

            $data = [
                'model_id' => $event->model->id,
                'model_type' => get_class($event->model),
                'locale' => $locale,
                'path' => collect($path)->flatten()->first(),
                'search_string' => implode(' ', $indexableKeys),
            ];

            SearchTerm::updateOrCreate([
                'model_id' => $data['model_id'],
                'model_type' => $data['model_type'],
                'locale' => $data['locale'],
            ], $data);
        });
    }

    protected function array_value_recursive(Collection $collection, ?array $keys = null, bool $unique = true): array
    {
        $arr = $collection->toArray();
        array_walk_recursive($arr, function ($v, $k) use ($keys, &$val) {
            if (in_array($k, $keys)) {
                $val[] = strip_tags($v);
            }
        });

        return $unique ? array_unique($val ?? []) : $val ?? [];
    }
}
