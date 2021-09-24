<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Transformers\PageTreeOptionTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class PageTreeController extends ApiController
{

    use ApiControllerTrait;

    public function index(Request $request) : JsonResponse
    {
        $pageRoot = Page::whereNull('parent_id')->first();

        $tree = Page::orderBy('sortindex')
            ->with('template')
            ->descendantsOf($pageRoot->id)
            ->toTree();

        if($request->has('selectOptions')) {
            return $this->respondWithItem($tree, new PageTreeOptionTransformer);
        }


        return $this->respondWithArray([
            'data' => $tree
        ]);
    }


    public function update(Request $request) : JsonResponse
    {
        $pageRoot = Page::whereNull('parent_id')->first();

        $treeData = $request->tree;
        Page::rebuildSubtree($pageRoot, $treeData);

        return $this->respondWithSuccess('Tree updated successfully');
    }
}