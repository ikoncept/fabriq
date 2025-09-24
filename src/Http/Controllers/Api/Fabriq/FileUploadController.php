<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class FileUploadController extends ApiController
{
    use ApiControllerTrait;

    public function store(Request $request): JsonResponse
    {
        $file = Fabriq::getModelClass('file');
        $file->save();
        try {
            $file->saveMedia($request->has('url'));
        } catch (\Throwable $exception) {
            $file->delete();
            throw $exception;
        }

        return $this->respondWithArray($file->toArray());
    }
}
