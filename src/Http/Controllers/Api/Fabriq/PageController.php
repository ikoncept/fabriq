<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreatePageRequest;
use Ikoncept\Fabriq\Models\Page;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PageController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Return index of pages.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getModelClass('page')::RELATIONSHIPS);
        $pages = QueryBuilder::for(Fabriq::getFqnModel('page'))
            ->allowedSorts('name', 'slug', 'id', 'created_at', 'updated_at')
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::exact('template_id'),
            ])
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($pages, Fabriq::getTransformerFor('page'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getModelClass('page')::RELATIONSHIPS);

        $page = QueryBuilder::for(Fabriq::getFqnModel('page'))
            ->where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($page, Fabriq::getTransformerFor('page'));
    }

    /**
     * Update the specified resource.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getModelClass('page')::RELATIONSHIPS);

        $page = Fabriq::getModelClass('page')->where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();
        $page->name = $request->name;
        $page->touch();
        $page->localizedContent = $request->localizedContent;
        $page->updated_by = $request->user()->id;

        $page->save();

        return $this->respondWithItem($page, Fabriq::getTransformerFor('page'));
    }

    /**
     * Create a new resource.
     *
     * @param CreatePageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePageRequest $request)
    {
        $pageRoot = Fabriq::getModelClass('page')->whereNull('parent_id')
            ->select('id')
            ->firstOrFail();

        $page = new Page();
        $page->name = $request->name;
        $page->template_id = $request->template_id;
        $page->parent_id = $pageRoot->id;
        $page->updated_by = $request->user()->id;
        $page->save();

        return $this->respondWithItem($page, Fabriq::getTransformerFor('page'), 201);
    }

    /**
     * Create a new resource.
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $page = Fabriq::getModelClass('page')->where('id', $id)->firstOrFail();

        $page->delete();

        return $this->respondWithSuccess('Page has been deleted');
    }
}
