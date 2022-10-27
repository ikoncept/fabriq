<?php

namespace Ikoncept\Fabriq\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;

class NotificationDeleted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets;

    /**
     * The notification.
     *
     * @var mixed
     */
    public $notification;

    /**
     * Create a new event instance.
     *
     * @param  mixed  $notification
     * @return void
     */
    public function __construct($notification)
    {
        $this->notification = $notification;
    }

    public function broadcastAs(): string
    {
        return 'notification.deleted';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $prefix = config('fabriq.ws_prefix');

        return new PrivateChannel($prefix.'.user.'.$this->notification->user_id);
    }

    public function broadcastWith(): array
    {
        return [
            'notification' => $this->notification,
        ];
    }
}
