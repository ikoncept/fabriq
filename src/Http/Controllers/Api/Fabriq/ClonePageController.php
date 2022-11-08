<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Actions\ClonePage;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class ClonePageController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Create a new resource.
     *
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, int $id, ClonePage $clonePage)
    {
        $pageRoot = Fabriq::getModelClass('page')->whereNull('parent_id')
            ->where('name', 'root')
            ->select('id')
            ->firstOrFail();
        $pageToClone = Fabriq::getModelClass('page')->findOrFail($id);
        $page = Fabriq::getModelClass('page');

        $page = $clonePage($pageRoot, $pageToClone, $request->name ?? 'Kopia av '.$pageToClone->name);

        return $this->respondWithItem($page, Fabriq::getTransformerFor('page'), 201);
    }
}
