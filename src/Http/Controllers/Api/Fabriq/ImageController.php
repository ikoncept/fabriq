<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Http\Requests\UpdateImageRequest;
use Ikoncept\Fabriq\Models\Image;
use Ikoncept\Fabriq\QueryBuilders\ImageSort;
use Ikoncept\Fabriq\Transformers\ImageTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class ImageController extends ApiController
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
        $eagerLoad = $this->getEagerLoad(Image::RELATIONSHIPS);
        $images = QueryBuilder::for(Fabriq::getFqnModel('image'))
            ->allowedSorts([
                'id', 'created_at', 'updated_at', 'alt_text',
                AllowedSort::custom('file_name', new ImageSort()),
                AllowedSort::custom('size', new ImageSort())
            ])
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->has('mediaImages')
            ->with($eagerLoad)
            ->paginate($this->number);


        return $this->respondWithPaginator($images, new ImageTransformer);
    }


    /**
     * Get a single image
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, $id) : JsonResponse
    {
        $image = Fabriq::getFqnModel('image')::findOrFail($id);

        return $this->respondWithItem($image, new ImageTransformer);
    }

    public function update(UpdateImageRequest $request, int $id) : JsonResponse
    {
        $image = Fabriq::getFqnModel('image')::findOrFail($id);
        $image->fill($request->validated());
        $image->imageTags = $request->validated()['tags'] ?? [];
        $media = $image->getFirstmedia('images');
        $media->name = $request->name;
        $media->save();
        $image->save();

        return $this->respondWithItem($image, new ImageTransformer);
    }


    public function destroy(Request $request, int $id) : JsonResponse
    {
        $image = Fabriq::getFqnModel('image')::findOrFail($id);
        $image->delete();

        return $this->respondWithSuccess('The image has been deleted');
    }

}
