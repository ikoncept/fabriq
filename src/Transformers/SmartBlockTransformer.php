<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\SmartBlock;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class SmartBlockTransformer extends TransformerAbstract
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
     * @param  SmartBlock  $smartBlock
     * @return array
     */
    public function transform(SmartBlock $smartBlock)
    {
        return $smartBlock->toArray();
        // return [
        //     'id' => (int) $smartBlock->id,
        // ];
    }

    public function includeLocalizedContent(SmartBlock $smartblock): Item
    {
        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($smartblock));
    }

    /**
     * Include content.
     *
     * @param SmartBlock $smartblock
     * @return Item
     */
    public function includeContent(SmartBlock $smartblock): Item
    {
        $content = $smartblock->getFieldContent($smartblock->revision);

        return $this->item($content, new ContentTransformer());
    }
}
