<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Transformers\PageTreeOptionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class PageTreeController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $request): JsonResponse
    {
        $pageRoot = Fabriq::getModelClass('page')
            ->where('name', 'root')
            ->whereNull('parent_id')->first();

        $tree = Fabriq::getModelClass('page')->orderBy('sortindex')
            ->with('template')
            ->descendantsOf($pageRoot->id)
            ->toTree();

        if ($request->has('selectOptions')) {
            return $this->respondWithItem($tree, new PageTreeOptionTransformer);
        }

        return $this->respondWithArray([
            'data' => $tree,
        ]);
    }

    public function update(Request $request): JsonResponse
    {
        $pageRoot = Fabriq::getModelClass('page')->whereNull('parent_id')
            ->first();

        $treeData = $request->tree;
        Fabriq::getModelClass('page')->rebuildSubtree($pageRoot, $treeData);

        return $this->respondWithSuccess('Tree updated successfully');
    }
}
