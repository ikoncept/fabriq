<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Actions\Fortify\UpdateUserPassword;
use Ikoncept\Fabriq\Actions\Fortify\UpdateUserProfileInformation;
use Ikoncept\Fabriq\Http\Controllers\Controller;
use Ikoncept\Fabriq\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class AuthenticatedUserController extends Controller
{
    use ApiControllerTrait;

    public function index(Request $request) : JsonResponse
    {
        return $this->respondWithItem($request->user(), new UserTransformer);
    }

    public function update(Request $request) : JsonResponse
    {
        $user = $request->user();
        if ($request->input('password', false)) {
            $updateUserPassword = new UpdateUserPassword();
            $updateUserPassword->update($user, $request->all());
        }

        // Update the profile
        (new UpdateUserProfileInformation())->update($user, $request->all());

        return $this->respondWithItem($request->user(), new UserTransformer);
    }
}
