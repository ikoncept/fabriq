<?php

namespace Ikoncept\Fabriq\Transformers;

use Kalnoy\Nestedset\Collection;
use League\Fractal\TransformerAbstract;

class MenuTreeTransformer extends TransformerAbstract
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
     * @param  Collection  $menuItem
     * @return array
     */
    public function transform(Collection $menuItem)
    {
        return $menuItem->toArray();
    }
}
