<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreatePageRequest;
use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Transformers\PageTransformer;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class ClonePageController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Create a new resource.
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, int $id)
    {
        $pageToClone = Fabriq::getModelClass('page')->findOrFail($id);
        // dd($pageToClone->toArray());
        $page = Fabriq::getModelClass('page');
        $page->name = 'Kopia av '.$pageToClone->name;
        $page->template_id = $pageToClone->template_id;
        $page->revision = $pageToClone->revision;
        $page->published_version = $pageToClone->published_version;
        $page->parent_id = $pageToClone->parent_id;
        $page->updated_by = $request->user()->id;
        $page->save();
        $page->localizedContent = $request->localizedContent;
        $page->save();

        return $this->respondWithItem($page, new PageTransformer, 201);
    }
}
