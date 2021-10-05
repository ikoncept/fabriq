<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class NotificationTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'notifiable'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Model  $notification
     * @return array
     */
    public function transform(Model $notification)
    {
        return $notification->toArray();
        // return [
        //     'id' => (int) $notification->id,
        // ];
    }

    /**
     * Include notifiable
     *
     * @param Model $notification
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeNotifiable(Model $notification)
    {
        if(! $notification->notifiable) {
            return $this->null();
        }

        return $this->item($notification->notifiable, new NotifiableTransformer);
    }
}
