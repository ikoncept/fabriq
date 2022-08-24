<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Models\Menu;
use Ikoncept\Fabriq\Models\MenuItem;
use Ikoncept\Fabriq\Repositories\Decorators\CachingMenuRepository;
use Ikoncept\Fabriq\Transformers\MenuTreeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class MenuItemTreeController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Return index of the resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function index(Request $request, int $id): JsonResponse
    {
        $menuItemRoot = MenuItem::where('menu_id', $id)
            ->whereNull('parent_id')
            ->first();
        $tree = MenuItem::orderBy('sortindex')
            ->descendantsOf($menuItemRoot->id)
            ->toTree();

        return $this->respondWithItem($tree, new MenuTreeTransformer());
    }

    /**
     * Update the resoource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $menuItemRoot = MenuItem::where('menu_id', $id)
            ->whereNull('parent_id')
            ->first();

        $treeData = $request->tree;
        MenuItem::rebuildSubtree($menuItemRoot, $treeData);

        return $this->respondWithSuccess('Tree updated successfully');
    }

    /**
     * Return specific menu.
     *
     * @param Request $request
     * @param string $slug
     * @return JsonResponse
     */
    public function show(CachingMenuRepository $repo, Request $request, string $slug): JsonResponse
    {
        return $this->respondWithArray($repo->findBySlug($slug));
    }
}
