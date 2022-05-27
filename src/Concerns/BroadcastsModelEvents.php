<?php

namespace Ikoncept\Fabriq\Concerns;

use Illuminate\Broadcasting\Channel;
use Illuminate\Database\Eloquent\BroadcastsEvents;
use Illuminate\Support\Str;
use ReflectionClass;

trait BroadcastsModelEvents
{
    use BroadcastsEvents;

    /**
     * Get the channels that model events should broadcast on.
     *
     * @param  string  $event
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn($event)
    {
        $prefix = config('fabriq.ws_prefix');
        $reflect = new ReflectionClass($this);
        $classSlug = Str::slug($reflect->getShortName());

        return [new Channel($prefix. '-' . $classSlug), new Channel($prefix. '-' . $classSlug . '.' . $this->id)];
    }

    public function broadcastWith() : array
    {
        $updatedBy = [];
        if($this->updatedByUser) {
            $updatedBy = [
                'updatedByName' => $this->updatedByUser->name
            ];
        }
        return [
            'model' => array_merge($this->toArray(), $updatedBy),
        ];
    }
}
