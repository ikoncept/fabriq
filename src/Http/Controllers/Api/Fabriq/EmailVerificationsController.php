<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class EmailVerificationsController extends ApiController
{
    use ApiControllerTrait;

    public function store(Request $request) : JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return $this->respondWithSuccess('Email verification request sent');
    }
}
