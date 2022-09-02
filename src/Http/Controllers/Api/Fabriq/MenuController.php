<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Menu;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class MenuController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Get index of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('menu')::RELATIONSHIPS);
        $paginator = Fabriq::getFqnModel('menu')::with($eagerLoad)->paginate($this->number);

        return $this->respondWithPaginator($paginator, Fabriq::getTransformerFor('menu'));
    }

    /**
     * Get a single resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Menu::RELATIONSHIPS);
        $menu = Fabriq::getFqnModel('menu')::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($menu, Fabriq::getTransformerFor('menu'));
    }

    /**
     * Create a new resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $menu = Fabriq::getModelClass('menu');
        $menu->name = $request->name;
        $menu->save();

        Fabriq::getFqnModel('menuItem')::create([
            'menu_id' => $menu->id,
        ]);

        return $this->respondWithItem($menu, Fabriq::getTransformerFor('menu'), 201);
    }

    /**
     * Update a resource.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function update(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('menu')::RELATIONSHIPS);
        $menu = Fabriq::getFqnModel('menu')::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();
        $menu->name = $request->name;
        $menu->save();

        return $this->respondWithItem($menu, Fabriq::getTransformerFor('menu'));
    }

    /**
     * Destroy a resource.
     *
     * @param int $id
     * @return JsonResponse
     */
    public function destroy(int $id): JsonResponse
    {
        $menu = Fabriq::getFqnModel('menu')::where('id', $id)->firstOrFail();
        $menu->delete();

        return $this->respondWithSuccess('Menu has been deleted');
    }
}
