<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Image;
use Ikoncept\Fabriq\Transformers\ImageTransformer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use Illuminate\Support\Str;
use InvalidArgumentException;

class ImageablesController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Get associated images for another model
     *
     * @param Request $request
     * @param string $model
     * @param int $modelId
     * @return JsonResponse
     * @throws InvalidArgumentException
     */
    public function index(Request $request, $model, $modelId) : JsonResponse
    {
        $guess = Str::studly(Str::singular($model));
        $relatedModelClass = '\\App\\Models\\' . $guess;

        if(! class_exists($relatedModelClass)) {
            throw new InvalidArgumentException('The related model was not found, you might want to add a mapping in your Image::class');
        }

        $relatedModel = $relatedModelClass::findOrFail($modelId);

        return $this->respondWithCollection($relatedModel->images, new ImageTransformer());
    }

    /**
     * Associate an image with another model
     *
     * @param Request $request
     * @param int $imageId
     * @param string $model
     * @return JsonResponse
     */
    public function store(Request $request, $imageId, $model) : JsonResponse
    {
        $modelId = $request->model_id;
        $image = Image::findOrFail($imageId);
        $guess = Str::studly(Str::singular($model));
        $relatedModelClass = '\\App\\Models\\' . $guess;

        if(! class_exists($relatedModelClass)) {
            throw new InvalidArgumentException('The related model was not found, you might want to add a mapping in your Image::class');
        }

        $relatedModel = $relatedModelClass::findOrFail($modelId);

        try {
            $relatedModel->images()->attach($image, ['sortindex' => $relatedModel->images->count()]);
        } catch (Exception $e) {
            return $this->errorWrongArgs('Image has no relation to ' . $model);
        }

        return $this->respondWithItem($image, new ImageTransformer(), 201);
    }
}
