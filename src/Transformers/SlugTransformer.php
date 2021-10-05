<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class SlugTransformer extends TransformerAbstract
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
     * @param  Model  $slug
     * @return array
     */
    public function transform(Model $slug)
    {
        return $slug->toArray();
        // return [
        //     'id' => (int) $slug->id,
        // ];
    }
}
