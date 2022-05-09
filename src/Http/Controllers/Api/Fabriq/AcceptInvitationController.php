<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Controllers\Controller;
use Ikoncept\Fabriq\Http\Requests\AcceptInvitationRequest;
use Ikoncept\Fabriq\Models\Invitation;
use Ikoncept\Fabriq\Transformers\InviteTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class AcceptInvitationController extends Controller
{
    use ApiControllerTrait;

    public function show(Request $request, int $tenantId, string $invitationUuid) : JsonResponse
    {
        if (! $request->hasValidSignature()) {
            return $this->errorUnauthorized();
        }

        $invitation = Invitation::where('uuid', $invitationUuid)
            ->where('tenant_id', $tenantId)
            ->with('user')
            ->firstOrFail();


        return $this->respondWithItem($invitation, new InviteTransformer());
    }

    public function store(AcceptInvitationRequest $request, string $invitationUuid) : JsonResponse
    {
        $invitation = Invitation::where('uuid', $invitationUuid)
            ->with('user')
            ->firstOrFail();

        $user = Fabriq::getFqnModel('user')::findOrFail($invitation->user_id);
        $user->email_verified_at = now();
        $user->password = bcrypt($request->password);
        $user->save();

        $invitation->delete();

        return $this->respondWithSuccess('The user has accepted the invitation successfully');
    }
}
