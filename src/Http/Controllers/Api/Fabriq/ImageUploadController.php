<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Http\Requests\UploadImageRequest;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class ImageUploadController extends ApiController
{
    use ApiControllerTrait;

    public function store(UploadImageRequest $request) : JsonResponse
    {
        $image = new Image();
        $image->save();
        try {
            $image->saveMedia($request->has('url'));
        } catch (\Throwable $exception) {
            $image->delete();

            return $this->setStatusCode(500)
                ->respondWithArray(['message' => 'Kunde inte ladda upp filen']);
        }

        return $this->respondWithArray($image->toArray());
    }
}
