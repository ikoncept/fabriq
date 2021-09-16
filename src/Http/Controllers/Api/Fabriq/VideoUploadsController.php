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
        $media = $video->addMediaFromRequest('video')
            ->toMediaCollection('videos');

        return $this->respondWithArray($video->toArray());
    }
}
