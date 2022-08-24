<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Http\Requests\UpdateMenuItemRequest;
use Ikoncept\Fabriq\Models\Menu;
use Ikoncept\Fabriq\Models\MenuItem;
use Ikoncept\Fabriq\Transformers\MenuItemTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class MenuItemController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Show a single item.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(MenuItem::RELATIONSHIPS);
        $item = MenuItem::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($item, new MenuItemTransformer());
    }

    /**
     * Update a menu item.
     *
     * @param UpdateMenuItemRequest $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(UpdateMenuItemRequest $request, int $id): JsonResponse
    {
        $item = MenuItem::findOrFail($id);
        $item->updateMetaContent($request->content);
        $item->page_id = $request->input('item.page_id');
        $item->type = $request->input('item.type');
        $item->page_id = $request->input('item.page_id');
        if ($item->type === 'external') {
            $item->page_id = null;
        }
        $item->save();

        return $this->respondWithItem($item, new MenuItemTransformer());
    }

    /**
     * Store a new menu item.
     *
     * @param UpdateMenuItemRequest $request
     * @param int $menuId
     * @return JsonResponse
     */
    public function store(UpdateMenuItemRequest $request, int $menuId): JsonResponse
    {
        $menuItemRoot = MenuItem::where('menu_id', $menuId)
            ->whereNull('parent_id')
            ->first();
        $menuItem = new MenuItem();
        $menuItem->page_id = $request->input('item.page_id');
        $menuItem->parent_id = $menuItemRoot->id;
        $menuItem->menu_id = $menuId;
        $menuItem->type = $request->input('item.type');
        $menuItem->save();
        $menuItem->updateMetaContent($request->content);

        return $this->respondWithItem($menuItem, new MenuItemTransformer, 201);
    }

    /**
     * Delete a menu item.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $menuItem = MenuItem::findOrFail($id);
        $menuItem->delete();

        return $this->respondWithSuccess('The menu item has been deleted');
    }
}
