<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Invitation;
use League\Fractal\TransformerAbstract;

class InviteTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Invitation  $invitation
     * @return array
     */
    public function transform(Invitation $invitation)
    {
        return $invitation->toArray();
        // return [
        //     'id' => (int) $invitation->id,
        // ];
    }
}
