<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Mail\AccountInvitation;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\BlockType;
use Ikoncept\Fabriq\Models\Invitation;
use Ikoncept\Fabriq\Transformers\BlockTypeTransformer;
use Ikoncept\Fabriq\Transformers\InvitationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\QueryBuilder;

class InvitationController extends ApiController
{
    use ApiControllerTrait;

    public function store(Request $request, int $userId) : JsonResponse
    {
        $user = Fabriq::getFqnModel('user')::where('id', $userId)
            ->firstOrFail();

        $invitation = $user->createInvitation(auth()->user()->id);
        $invitation->load('invitedBy', 'user');

        Mail::to($user->email)
            ->queue(new AccountInvitation($invitation));

        return $this->respondWithItem($invitation, new InvitationTransformer(), 201);
    }

    public function destroy(Request $request, int $userId) : JsonResponse
    {
        $invitation = Invitation::where('user_id', $userId)
            ->firstOrFail();

        $invitation->delete();

        return $this->respondWithSuccess('Invitation was deleted successfully');
    }
}
