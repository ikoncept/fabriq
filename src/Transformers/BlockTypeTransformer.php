<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\BlockType;
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
     * @param  BlockType  $blockType
     * @return array
     */
    public function transform(BlockType $blockType)
    {
        return $blockType->toArray();
        // return [
        //     'id' => (int) $blockType->id,
        // ];
    }
}
