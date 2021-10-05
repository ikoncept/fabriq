<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
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
     * @param Model $menu
     * @return array
     */
    public function transform(Model $menu)
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
