<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Video;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class VideoUploadsController extends ApiController
{
    use ApiControllerTrait;


    public function store(Request $request) : JsonResponse
    {
        $video = new Video();
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
