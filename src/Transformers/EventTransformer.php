<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Event;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class EventTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'localizedContent', 'content',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Event  $event
     * @return array
     */
    public function transform(Event $event)
    {
        return [
            'id' => (int) $event->id,
            'title' => (string) $event->title,
            'start' => (string) ($event->start) ? $event->start->toISOString() : '',
            'end' => (string) ($event->end) ? $event->end->toISOString() : '',
            'start_time' => (string) $event->start_time,
            'end_time' => (string) $event->end_time,
            'daily_interval' => (int) $event->daily_interval,
            'has_interval' => (bool) $event->daily_interval,
            'updated_at' => $event->updated_at->toISOString(),
        ];
    }

    public function includeContent(Event $event): Item
    {
        $content = $event->getFieldContent();

        return $this->item($content, new ContentTransformer());
    }

    public function includeLocalizedContent(Event $event): Item
    {
        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($event));
    }
}
