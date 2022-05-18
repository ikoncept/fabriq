<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\File;
use Ikoncept\Fabriq\QueryBuilders\FileSort;
use Ikoncept\Fabriq\Transformers\FileTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

class FileController extends ApiController
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
        $eagerLoad = $this->getEagerLoad(File::RELATIONSHIPS);
        $files = QueryBuilder::for(Fabriq::getFqnModel('file'))
            ->allowedSorts([
                'id', 'created_at', 'updated_at', 'alt_text',
                AllowedSort::custom('file_name', new FileSort()),
                AllowedSort::custom('size', new FileSort())
            ])
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->with($eagerLoad)
            ->paginate($this->number);


        return $this->respondWithPaginator($files, new FileTransformer);
    }

    public function show(Request $request, int $id) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(File::RELATIONSHIPS);
        $file = File::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($file, new FileTransformer);
    }

    public function update(Request $request, int $id) : JsonResponse
    {
        $file = Fabriq::getFqnModel('file')::findOrFail($id);

        $file->readable_name = $request->readable_name;
        $file->caption = $request->caption;
        $file->fileTags = $request->tags;
        $media = $file->getFirstmedia('files');
        $media->name = $request->name;
        $media->save();
        $file->save();

        return $this->respondWithItem($file, new FileTransformer);
    }

    public function destroy(Request $request, int $id) : JsonResponse
    {
        $file = Fabriq::getFqnModel('file')::findOrFail($id);
        $file->delete();

        return $this->respondWithSuccess('The file has been deleted');
    }
}
