<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Notification;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected $availableIncludes = [
        'notifiable',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Notification  $notification
     * @return array
     */
    public function transform(Notification $notification)
    {
        return $notification->toArray();
        // return [
        //     'id' => (int) $notification->id,
        // ];
    }

    /**
     * Include notifiable.
     *
     * @param Notification $notification
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeNotifiable(Notification $notification)
    {
        if (! $notification->notifiable) {
            return $this->null();
        }

        return $this->item($notification->notifiable, new NotifiableTransformer);
    }
}
