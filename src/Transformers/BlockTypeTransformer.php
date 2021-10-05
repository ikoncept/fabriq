<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class BlockTypeTransformer extends TransformerAbstract
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
     * @param  Model  $blockType
     * @return array
     */
    public function transform(Model $blockType)
    {
        return $blockType->toArray();
        // return [
        //     'id' => (int) $blockType->id,
        // ];
    }
}
