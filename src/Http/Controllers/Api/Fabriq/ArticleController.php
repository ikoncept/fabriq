<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Controllers\Controller;
use Ikoncept\Fabriq\Http\Requests\CreateArticleRequest;
use Ikoncept\Fabriq\Http\Requests\UpdateArticleRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleController extends Controller
{
    use ApiControllerTrait;

    public function index(Request $request): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('article')::RELATIONSHIPS);
        $articles = QueryBuilder::for(Fabriq::getFqnModel('article'))
            ->allowedSorts(['name', 'updated_at', 'publishes_at'])
            ->allowedFilters([
                AllowedFilter::scope('search'),
                AllowedFilter::scope('published'),
            ])
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($articles, Fabriq::getTransformerFor('article'));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('article')::RELATIONSHIPS);
        $article = Fabriq::getFqnModel('article')::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($article, Fabriq::getTransformerFor('article'));
    }

    public function store(CreateArticleRequest $request): JsonResponse
    {
        $article = Fabriq::getModelClass('article');
        $article->fill($request->validated());
        $article->template_id = 2;
        $article->save();

        return $this->respondWithItem($article, Fabriq::getTransformerFor('article'), 201);
    }

    public function update(UpdateArticleRequest $request, int $id): JsonResponse
    {
        $article = Fabriq::getFqnModel('article')::findOrFail($id);
        $article->fill($request->validated());
        $article->updateContent($request->content);
        $article->save();

        return $this->respondWithItem($article, Fabriq::getTransformerFor('article'));
    }

    public function destroy(int $id): JsonResponse
    {
        $article = Fabriq::getFqnModel('article')::findOrFail($id);
        $article->delete();

        return $this->respondWithSuccess('Article deleted successfully');
    }
}
