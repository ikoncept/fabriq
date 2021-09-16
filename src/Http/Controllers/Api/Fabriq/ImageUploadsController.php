<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class ImageUploadsController extends ApiController
{
    use ApiControllerTrait;

    public function store(Request $request) : JsonResponse
    {
        $image = new Image();
        $image->save();
        try {
            $image->addMediaFromRequest('image')
                ->withResponsiveImages()
                ->toMediaCollection('images');
        } catch (\Throwable $exception) {
            $image->delete();
            throw $exception;
        }

        return $this->respondWithArray($image->toArray());
    }
}
