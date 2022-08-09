<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\MenuItem;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class MenuItemTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected $availableIncludes = [
        'content', 'page', 'localizedContent',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  MenuItem  $menuItem
     * @return array
     */
    public function transform(MenuItem $menuItem)
    {
        return [
            'id' => (int) $menuItem->id,
            'type' => (string) $menuItem->type,
            'parent_id' => $menuItem->parent_id,
            'is_external' => (bool) $menuItem->is_external,
            'redirect' => (bool) $menuItem->redirect,
            'external_url' => (string) $menuItem->external_url,
            'page_id' =>  $menuItem->page_id,
            'created_at' => (string) $menuItem->created_at,
            'updated_at' => (string) $menuItem->updated_at,
        ];
    }

    /**
     * Include content.
     *
     * @param MenuItem $menuItem
     * @return Item
     */
    public function includeContent(MenuItem $menuItem) : Item
    {
        $content = $menuItem->getFieldContent();

        return $this->item($content, new ContentTransformer());
    }

    public function includePage(MenuItem $menuItem) : Item
    {
        return $this->item($menuItem->page, new PageTransformer);
    }

    public function includeLocalizedContent(MenuItem $menuItem) : Item
    {
        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($menuItem));
    }
}
