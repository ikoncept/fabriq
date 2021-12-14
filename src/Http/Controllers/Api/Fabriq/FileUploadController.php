<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\File;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class FileUploadController extends ApiController
{
    use ApiControllerTrait;

    public function store(Request $request) : JsonResponse
    {
        $file = new File();
        $file->save();
        try {
            $file->addMediaFromRequest('file')
                ->toMediaCollection('files');
        } catch (\Throwable $exception) {
            $file->delete();
            throw $exception;
        }

        return $this->respondWithArray($file->toArray());
    }
}
