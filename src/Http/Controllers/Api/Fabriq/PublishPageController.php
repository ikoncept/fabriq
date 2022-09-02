<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Page;
use Illuminate\Http\JsonResponse;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class PublishPageController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Publish a page revision.
     *
     * @param int $pageId
     * @return JsonResponse
     */
    public function store(int $pageId): JsonResponse
    {
        $page = Fabriq::getFqnModel('page')::withoutEvents(function () use ($pageId) {
            $page = Fabriq::getFqnModel('page')::findOrFail($pageId);
            $page->publish($page->revision);

            return $page;
        });

        return $this->respondWithItem($page, Fabriq::getTransformerFor('page'));
    }
}
