<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class GroupedTemplateFieldTransformer extends TransformerAbstract
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
     * @param  Collection  $collection
     * @return array
     */
    public function transform(Collection $collection) : array
    {
        return $collection->toArray();
    }
}
