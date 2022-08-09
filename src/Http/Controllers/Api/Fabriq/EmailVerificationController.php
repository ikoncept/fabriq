<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class EmailVerificationController extends ApiController
{
    use ApiControllerTrait;

    public function store(Request $request) : JsonResponse
    {
        $request->user()->sendEmailVerificationNotification();

        return $this->respondWithSuccess('Email verification request sent');
    }
}
