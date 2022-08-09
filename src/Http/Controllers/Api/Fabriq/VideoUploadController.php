<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class VideoUploadController extends ApiController
{
    use ApiControllerTrait;

    public function store(Request $request) : JsonResponse
    {
        $video = Fabriq::getModelClass('video');
        $video->save();
        try {
            $video->addMediaFromRequest('video')
                ->toMediaCollection('videos');
        } catch (\Throwable $exception) {
            $video->delete();
            throw $exception;
        }

        return $this->respondWithArray($video->toArray());
    }
}
