<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class SmartBlockTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'localizedContent', 'content'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param Model $smartBlock
     * @return array
     */
    public function transform(Model $smartBlock)
    {
        return $smartBlock->toArray();
        // return [
        //     'id' => (int) $smartBlock->id,
        // ];
    }

    public function includeLocalizedContent(Model $smartblock) : Item
    {
        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($smartblock));
    }

    /**
     * Include content
     *
     * @param Model $smartblock
     * @return Item
     */
    public function includeContent(Model $smartblock) : Item
    {
        $content = $smartblock->getFieldContent($smartblock->revision);

        return $this->item($content, new ContentTransformer());
    }
}
