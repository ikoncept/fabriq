<?php

namespace Ikoncept\Fabriq\Listeners;

use Ikoncept\Fabriq\Models\Slug;
use Illuminate\Support\Str;

class UpdateSlugListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if (! $event->model->templateSluggable) {
            return;
        }
        $sluggableDefinitions = $event->definitions->filter(function ($def, $key) use ($event) {
            return $key === implode(',', $event->model->templateSluggable);
            // return Str::contains(implode(',', $event->model->templateSluggable), $key);
        });

        foreach ($sluggableDefinitions as $item) {
            $slug = Slug::firstOrNew([
                'model_id' => $event->model->id,
                'locale' => $item['definition']->locale,
                'model_type' => $event->model->getMorphClass(),
            ], [
                'source_key' => $item['term']->key,
            ]);
            if (! $item['definition']->content) {
                $item['definition']->content = 'no_slug_yet_'.Str::random(6);
            }
            $slug->source_string = $item['definition']->content;
            $slug->source_key = $item['term']->key;
            $slug->locale = $item['definition']->locale;
            $slug->model_id = $event->model->id;
            $slug->save();
        }
    }
}
