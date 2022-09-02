<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Controllers\Controller;
use Ikoncept\Fabriq\Http\Requests\CreateUserImageRequest;
use Ikoncept\Fabriq\Models\Image;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class UserImageController extends Controller
{
    use ApiControllerTrait;

    public function store(CreateUserImageRequest $request): JsonResponse
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

        return $this->respondWithItem($request->user(), Fabriq::getTransformerFor('user'));
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $user->image->delete();
        $user->image_id = null;
        $user->save();

        return $this->respondWithSuccess('Image was deleted successfully');
    }
}
