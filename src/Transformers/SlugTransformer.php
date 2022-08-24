<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Slug;
use League\Fractal\TransformerAbstract;

class SlugTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected array $availableIncludes = [
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Slug  $slug
     * @return array
     */
    public function transform(Slug $slug)
    {
        return $slug->toArray();
        // return [
        //     'id' => (int) $slug->id,
        // ];
    }
}
