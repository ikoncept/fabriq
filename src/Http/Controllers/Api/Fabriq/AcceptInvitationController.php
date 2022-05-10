<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Controllers\Controller;
use Ikoncept\Fabriq\Http\Requests\AcceptInvitationRequest;
use Ikoncept\Fabriq\Models\Invitation;
use Ikoncept\Fabriq\Transformers\InvitationTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Infab\Core\Traits\ApiControllerTrait;

class AcceptInvitationController extends Controller
{
    use ApiControllerTrait;

    /**
     * Show invitation view
     *
     * @param Request $request
     * @param string $invitationUuid
     * @return JsonResponse|View
     */
    public function show(Request $request, string $invitationUuid)
    {
        if (! $request->hasValidSignature()) {
            return $this->errorUnauthorized();
        }

        $invitation = Invitation::where('uuid', $invitationUuid)
            ->with('user')
            ->firstOrFail();


        /** @var view-string $viewString **/
        $viewString = 'vendor.fabriq.auth.activate';

        return view($viewString, ['invitation' => $invitation]);
    }

    /**
     * Undocumented function
     *
     * @param AcceptInvitationRequest $request
     * @param string $invitationUuid
     * @return JsonResponse|RedirectResponse
     */
    public function store(AcceptInvitationRequest $request, string $invitationUuid)
    {
        if (! $request->hasValidSignature()) {
            return $this->errorUnauthorized();
        }

        $invitation = Invitation::where('uuid', $invitationUuid)
            ->with('user')
            ->firstOrFail();

        $user = Fabriq::getFqnModel('user')::findOrFail($invitation->user_id);
        $user->email_verified_at = now();
        $user->password = bcrypt($request->password);
        $user->save();
        Auth::login($user);

        $invitation->delete();

        if(request()->wantsJson()) {
            return $this->respondWithSuccess('The user has accepted the invitation successfully');
        }

        return response()->redirectTo('/');
    }
}
