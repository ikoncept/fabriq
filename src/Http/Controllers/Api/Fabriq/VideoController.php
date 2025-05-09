<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Video;
use Ikoncept\Fabriq\QueryBuilders\TagSort;
use Ikoncept\Fabriq\QueryBuilders\VideoSort;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class VideoController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $request): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Video::RELATIONSHIPS);
        $videos = QueryBuilder::for(Fabriq::getFqnModel('video'))
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->allowedSorts([
                'id', 'created_at', 'updated_at', 'alt_text',
                AllowedSort::custom('file_name', new VideoSort),
                AllowedSort::custom('size', new VideoSort),
                AllowedSort::custom('tags', new TagSort, 'videos'),
            ])
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($videos, Fabriq::getTransformerFor('video'));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Video::RELATIONSHIPS);

        $video = Fabriq::getFqnModel('video')::where('id', $id)->with($eagerLoad)->firstOrFail();

        return $this->respondWithItem($video, Fabriq::getTransformerFor('video'));
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad();
        $video = Fabriq::getFqnModel('video')::where('id', $id)->with($eagerLoad)->firstOrFail();
        $video->alt_text = $request->alt_text;
        $video->caption = $request->caption;

        $video->videoTags = $request->tags;

        $media = $video->getFirstmedia('videos');
        $media->name = $request->name;
        $media->save();

        $video->save();

        return $this->respondWithItem($video, Fabriq::getTransformerFor('video'));
    }

    public function destroy(int $id): JsonResponse
    {
        $video = Fabriq::getFqnModel('video')::findOrFail($id);
        $video->delete();

        return $this->respondWithSuccess('Video has been deleted');
    }
}
