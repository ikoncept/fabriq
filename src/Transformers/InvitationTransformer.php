<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Invitation;
use League\Fractal\TransformerAbstract;

class InvitationTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected array $availableIncludes = [
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Invitation  $invitation
     * @return array
     */
    public function transform(Invitation $invitation)
    {
        return array_merge([
            'is_valid' => (bool) $invitation->created_at->diffInHours(now()) < 48,
        ], $invitation->toArray());
    }
}
