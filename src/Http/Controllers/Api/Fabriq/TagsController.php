<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Image;
use Ikoncept\Fabriq\Transformers\TagTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\Tags\Tag;

class TagsController extends ApiController
{
    use ApiControllerTrait;

    const TAGGABLE_TYPES = [
        'images' => 'Ikoncept\Fabriq\Models\Image',
        'files' => 'Ikoncept\Fabriq\Models\File',
        'videos' => 'Ikoncept\Fabriq\Models\Video',
        'contacts' => 'Ikoncept\Fabriq\Models\Contact',
    ];

    public function index(Request $request) : JsonResponse
    {
        $tags = QueryBuilder::for(Fabriq::getFqnModel('tag'))
            ->allowedFilters([
                AllowedFilter::scope('type', 'withType')
            ])
            ->get();

        return $this->respondWithCollection($tags, new TagTransformer);
    }

    /**
     * Associate a model with a tag
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request) : JsonResponse
    {
        $modelName = self::TAGGABLE_TYPES[$request->model_type] ?? null;
        if(! $modelName) {
            return $this->errorWrongArgs('This type is not taggable');
        }

        $tags = $request->tags;
        foreach($tags as $tag) {
            $newTag = Tag::findOrCreate($tag, $request->model_type);
            $newTag->save();
        }

        $modelName::whereIn('id', $request->models)
            ->select('id')
            ->get()
            ->each(function($item) use ($tags, $request) {
                $item->attachTags($tags, $request->model_type);
            });

        return $this->respondWithSuccess('Tags was attached');
    }
}
