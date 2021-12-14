<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Http\Requests\CreatePageRequest;
use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Transformers\PageTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use Infab\TranslatableRevisions\Models\I18nLocale;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class PageController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Return index of pages
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
                AllowedFilter::scope('search')
            ])
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($pages, new PageTransformer());
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
            ->allowedAppends(['paths'])
            ->where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($page, new PageTransformer);
    }

    /**
     * Update the specified resource
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

        $page->save();

        return $this->respondWithItem($page, new PageTransformer);
    }

    /**
     * Create a new resource
     *
     * @param CreatePageRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(CreatePageRequest $request)
    {
        $pageRoot = Fabriq::getModelClass('page')->whereNull('parent_id')
            ->select('id')
            ->first();

        $page = new Page();
        $page->name = $request->name;
        $page->template_id = $request->template_id;
        $content = [
            'page_title' => $request->name
        ];
        $page->parent_id = $pageRoot->id;
        $page->save();

        $supportedLocales = Fabriq::getModelClass('locale')->cachedLocales();
        $supportedLocales->each(function($locale, $key) use ($content, $page) {
            $page->updateContent($content, $key, 1);
        });


        return $this->respondWithItem($page, new PageTransformer, 201);
    }

    /**
     * Create a new resource
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
