<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class BustCacheController extends ApiController
{
    use ApiControllerTrait;

    public function store(): JsonResponse
    {
        Cache::flush();

        return $this->respondWithSuccess('Cache was purged successfully');
    }
}
