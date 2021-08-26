<?php

namespace Ikoncept\Fabriq\Transformers;

use League\Fractal\TransformerAbstract;
use Spatie\Permission\Models\Role;

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
     * @param  Role  $role
     * @return array
     */
    public function transform(Role $role)
    {
        return $role->toArray();
    }
}
