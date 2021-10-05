<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
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
     * @param  Model  $menuItem
     * @return array
     */
    public function transform(Model $menuItem) : array
    {
        return [
            'id' => $menuItem->id,
            'title' => $menuItem->title,
            'slug' => $menuItem->getSlugString(),
            'path' => $menuItem->relativePath,
            'localized_path' => '/' . app()->getLocale() . $menuItem->relativePath,
            'type' => $menuItem->type
        ];
    }

    public function includePage(Model $tree) : Item
    {
        return $this->item($tree->page, new PageTransformer);
    }

    public function includeChildren(Model $tree) : Collection
    {
        return $this->collection($tree->children, new MenuTreeItemTransformer);
    }

    /**
     * Include content
     *
     * @param Model $menuItem
     * @return Item
     */
    public function includeContent(Model $menuItem) : Item
    {
        $content = $menuItem->getFieldContent($menuItem->revision);

        return $this->item($content, new ContentTransformer());
    }
}
