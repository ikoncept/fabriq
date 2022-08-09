<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Models\User;
use Ikoncept\Fabriq\Notifications\AskToLeaveNotification;
use Ikoncept\Fabriq\Notifications\LeaveDeclinedNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class DeclineToLeaveNotificationController extends ApiController
{
    use ApiControllerTrait;

    public function __invoke(Request $request, int $userId) : JsonResponse
    {
        $request->validate([
            'path' => 'required|max:255',
        ]);
        $causer = $request->user();
        $recipient = User::findOrFail($userId);

        $recipient->notify(new LeaveDeclinedNotification($causer, $request->path));

        return $this->respondWithSuccess('Editing rights was declined');
    }
}
