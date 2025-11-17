<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class ContactSortController extends ApiController
{
    use ApiControllerTrait;

    public function __invoke(Request $request)
    {
        $contacts = collect($request->contacts);

        foreach ($contacts as $contact) {
            Fabriq::getModelClass('contact')->where('id', $contact['id'])
                ->update(['sortindex' => $contact['sortindex']]);
        }

        return $this->respondWithSuccess('Contact order has been updated');
    }
}
