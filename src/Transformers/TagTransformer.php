<?php

namespace Ikoncept\Fabriq\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Tags\Tag;

class TagTransformer extends TransformerAbstract
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
     * @param  Tag  $tag
     * @return array
     */
    public function transform(Tag $tag)
    {
        return [
            'id' => $tag->id,
            'name' => $tag->name,
            'value' => $tag->id,
            'type' => $tag->type
        ];
    }
}
