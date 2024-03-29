<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class NotifiableTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'user', 'page',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Model  $notifiable
     * @return array
     */
    public function transform(Model $notifiable)
    {
        // $extras = [
        //     'class_name' => get_class($notifiable),
        //     // 'user' => [
        //     //     'data' => $notifiable->user
        //     // ]
        // ];
        return $notifiable->toArray();
    }

    /**
     * Include user.
     *
     * @param  Model  $notifiable
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includeUser(Model $notifiable)
    {
        if (! $notifiable->user) {
            return $this->null();
        }

        return $this->item($notifiable->user, Fabriq::getTransformerFor('user'));
    }

    /**
     * Include page.
     *
     * @param  Model  $notifiable
     * @return \League\Fractal\Resource\Item|\League\Fractal\Resource\NullResource
     */
    public function includePage(Model $notifiable)
    {
        if (! $notifiable->commentable) {
            return $this->null();
        }

        return $this->item($notifiable->commentable, Fabriq::getTransformerFor('page'));
    }
}
