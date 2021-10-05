<?php

namespace Ikoncept\Fabriq\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Database\Eloquent\Model;

class RoleTransformer extends TransformerAbstract
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
     * @param  Model  $role
     * @return array
     */
    public function transform(Model $role)
    {
        return $role->toArray();
    }
}
