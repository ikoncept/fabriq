<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Transformers\PageTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class PublishPagesController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Publish a page revision
     *
     * @param integer $pageId
     * @return JsonResponse
     */
    public function store(int $pageId) : JsonResponse
    {
        $page = Page::findOrFail($pageId);
        $page->publish($page->revision);

        return $this->respondWithItem($page, new PageTransformer);
    }
}
