<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Repositories\EloquentPageRepository;
use Ikoncept\Fabriq\Transformers\LivePageTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class PageSlugsController extends ApiController
{
    use ApiControllerTrait;

    public function show(EloquentPageRepository $repo, Request $request, string $slug): JsonResponse
    {
        $result = $repo->findBySlug($slug);

        return $this->respondWithItem($result, new LivePageTransformer);
    }
}
