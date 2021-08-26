<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Menu;
use League\Fractal\TransformerAbstract;

class MenuTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Menu  $menu
     * @return array
     */
    public function transform(Menu $menu)
    {
        return [
            'id' => (int) $menu->id,
            'name' => $menu->name,
            'slug' => (string) $menu->slug,
            'created_at' => (string) $menu->created_at,
            'updated_at' => (string) $menu->updated_at,
        ];
    }
}
