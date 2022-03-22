<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\MenuItem;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class MenuTreeItemTransformer extends TransformerAbstract
{

    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'children', 'page', 'content'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  MenuItem  $menuItem
     * @return array
     */
    public function transform(MenuItem $menuItem) : array
    {
        return [
            'id' => $menuItem->id,
            'title' => $menuItem->title,
            'slug' => $menuItem->getSlugString(),
            'path' => $menuItem->relativePath,
            'localized_path' => '/' . app()->getLocale() . $menuItem->relativePath,
            'parent_id' => ($menuItem->parent_id) ? (int) $menuItem->parent_id : null,
            'type' => $menuItem->type
        ];
    }

    public function includePage(MenuItem $tree) : Item
    {
        return $this->item($tree->page, new PageTransformer);
    }

    public function includeChildren(MenuItem $tree) : Collection
    {
        return $this->collection($tree->children, new MenuTreeItemTransformer);
    }

    /**
     * Include content
     *
     * @param MenuItem $menuItem
     * @return Item
     */
    public function includeContent(MenuItem $menuItem) : Item
    {
        $content = $menuItem->getFieldContent($menuItem->revision);

        return $this->item($content, new ContentTransformer());
    }
}
