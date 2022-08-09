<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Models\Role;
use Ikoncept\Fabriq\Transformers\RoleTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class RoleController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $request) : JsonResponse
    {
        $roles = Role::orderBy('display_name')
            ->notHidden()
            ->get();

        return $this->respondWithCollection($roles, new RoleTransformer);
    }
}
