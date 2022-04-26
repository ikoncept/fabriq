<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Infab\Core\Traits\ApiControllerTrait;

class PagePathController
{
    use ApiControllerTrait;

    protected $middleware = [LocaleMiddleware::class];


    public function index(Request $request, int $id) : JsonResponse
    {
        $page = Fabriq::getModelClass('page')->whereId($id)
            ->with('slugs', 'menuItems', 'latestSlug')
            ->firstOrFail();

        $paths = $page->transformPaths();

        return $this->respondWithArray(['data' => $paths]);
    }
}
