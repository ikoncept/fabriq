<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'localizedContent', 'content', 'tags'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param Model $contact
     * @return array
     */
    public function transform(Model $contact)
    {
        return $contact->toArray();
    }

    public function includeLocalizedContent(Model $contact) : Item
    {
        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($contact));
    }

    /**
     * Include content
     *
     * @param Model $contact
     * @return Item
     */
    public function includeContent(Model $contact) : Item
    {
        $content = $contact->getFieldContent($contact->revision);

        return $this->item($content, new ContentTransformer());
    }

    public function includeTags(Model $contact) : Collection
    {
        return $this->collection($contact->tags, new TagTransformer);
    }

}
