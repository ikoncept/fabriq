<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Menu;
use Ikoncept\Fabriq\Models\MenuItem;
use Ikoncept\Fabriq\Transformers\MenuTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class MenuController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Get index of the resource
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Menu::RELATIONSHIPS);
        $paginator = Menu::with($eagerLoad)->paginate($this->number);

        return $this->respondWithPaginator($paginator, new MenuTransformer);
    }

    /**
     * Get a single resource
     *
     * @param Request $request
     * @param integer $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Menu::RELATIONSHIPS);
        $menu = Menu::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($menu, new MenuTransformer);
    }

    /**
     * Create a new resource
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $menu = new Menu();
        $menu->name = $request->name;
        $menu->save();

        MenuItem::create([
            'menu_id' => $menu->id
        ]);

        return $this->respondWithItem($menu, new MenuTransformer(), 201);
    }

    /**
     * Update a resource
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Menu::RELATIONSHIPS);
        $menu = Menu::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();
        $menu->name = $request->name;
        $menu->save();

        return $this->respondWithItem($menu, new MenuTransformer());
    }

    /**
     * Destroy a resource
     *
     * @param integer $id
     * @return JsonResponse
     */
    public function destroy(int $id) : JsonResponse
    {
        $menu = Menu::where('id', $id)->firstOrFail();
        $menu->delete();

        return $this->respondWithSuccess('Menu has been deleted');
    }
}
