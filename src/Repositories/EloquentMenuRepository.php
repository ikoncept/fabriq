<?php

namespace Ikoncept\Fabriq\Repositories;

use Ikoncept\Fabriq\Models\Menu;
use Ikoncept\Fabriq\Models\MenuItem;
use Ikoncept\Fabriq\Repositories\Interfaces\MenuRepositoryInterface;
use Ikoncept\Fabriq\Transformers\MenuTreeItemTransformer;
use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Scope;

class EloquentMenuRepository implements MenuRepositoryInterface
{
    /**
     * Menu model.
     *
     * @var mixed
     */
    private $model;

    public function __construct(Manager $fractal, MenuItem $model, Menu $menuModel)
    {
        $this->fractal = $fractal;
        $this->model = $model;
        $this->menuModel = $menuModel;
        $request = request();

        if ($request->has('include')) {
            $this->fractal->parseIncludes($request->input('include'));
        }
    }

    /**
     * Find by slug.
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug(string $slug)
    {
        $menu = $this->menuModel->where('slug', $slug)->firstOrFail();
        $menuItemRoot = MenuItem::where('menu_id', $menu->id)
            ->whereNull('parent_id')
            ->first();

        $tree = $this->model->orderBy('sortindex')
            ->with('ancestors', 'ancestors.page', 'ancestors.page.slugs')
            ->descendantsOf($menuItemRoot->id)
            ->toTree();

        $data = $this->getCollection($tree, new MenuTreeItemTransformer());

        return $data->toArray();
    }

    /**
     * Create data.
     *
     * @param object $collection
     * @param mixed $callback
     * @return Scope
     */
    public function getCollection($collection, $callback) : Scope
    {
        $resource = new Collection($collection, $callback, 'data');

        $scope = $this->fractal->createData($resource);

        return $scope;
    }
}
