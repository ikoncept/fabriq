<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Contact;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ContactTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected $availableIncludes = [
        'localizedContent', 'content', 'tags',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Contact  $contact
     * @return array
     */
    public function transform(Contact $contact)
    {
        return $contact->toArray();
    }

    public function includeLocalizedContent(Contact $contact) : Item
    {
        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($contact));
    }

    /**
     * Include content.
     *
     * @param Contact $contact
     * @return Item
     */
    public function includeContent(Contact $contact) : Item
    {
        $content = $contact->getFieldContent($contact->revision);

        return $this->item($content, new ContentTransformer());
    }

    public function includeTags(Contact $contact) : Collection
    {
        return $this->collection($contact->tags, new TagTransformer);
    }
}
