<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\UploadImageRequest;
use Illuminate\Http\JsonResponse;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class ImageUploadController extends ApiController
{
    use ApiControllerTrait;

    public function store(UploadImageRequest $request): JsonResponse
    {
        $image = Fabriq::getModelClass('image');
        $image->save();
        try {
            $image->saveMedia($request->has('url'));
        } catch (\Throwable $exception) {
            $image->delete();

            return $this->setStatusCode(500)
                ->respondWithArray([
                    'message' => 'Kunde inte ladda upp filen',
                    'exception' => $exception->getMessage(),
                ]);
        }

        return $this->respondWithArray(array_merge($image->toArray(), $image->getFirstMedia('images')->only('uuid')));
    }
}
