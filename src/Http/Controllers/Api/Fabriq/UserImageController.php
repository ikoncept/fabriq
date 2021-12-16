<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Http\Controllers\Controller;
use Ikoncept\Fabriq\Models\Image;
use Ikoncept\Fabriq\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class UserImageController extends Controller
{
    use ApiControllerTrait;

    public function store(Request $request) : JsonResponse
    {
        $image = new Image();
        $image->save();
        try {
            $image->saveMedia(false, 'profile_image');

            $user = $request->user();
            $user->image_id = $image->id;
            $user->save();

        } catch (\Throwable $exception) {
            $image->delete();

            return $this->setStatusCode(500)
                ->respondWithArray(['message' => 'Kunde inte ladda upp bilden']);
        }

        return $this->respondWithItem($request->user(), new UserTransformer);
    }

    public function delete(Request $request)
    {
        $user = $request->user();
        $user->image->delete();
        $user->image_id = null;
        $user->save();
    }
}
