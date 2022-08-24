<?php

namespace Ikoncept\Fabriq\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserMentionedInComment implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The comment posted.
     *
     * @var mixed
     */
    public $comment;

    /**
     * The notification.
     *
     * @var mixed
     */
    public $notification;

    /**
     * Create a new event instance.
     *
     * @param mixed $comment
     * @param mixed $notification
     * @return void
     */
    public function __construct($comment, $notification)
    {
        $this->comment = $comment;
        $this->notification = $notification;
    }

    public function broadcastAs(): string
    {
        return 'comment.user-mentioned';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $prefix = config('fabriq.ws_prefix');

        return [new PrivateChannel($prefix.'.user.'.$this->notification->user_id)];
    }

    public function broadcastWith(): array
    {
        return [
            'comment' => $this->comment,
            'notification' => $this->notification,
        ];
    }
}
