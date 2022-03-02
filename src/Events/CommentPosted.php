<?php

namespace Ikoncept\Fabriq\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CommentPosted implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * The comment posted
     *
     * @var mixed
     */
    public $comment;

    /**
     * The notification
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

    public function broadscastAs()
    {
        return 'comment.posted';
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.'. $this->notification->user_id);
    }

    public function broadcastWith()
    {
        return [
            'comment' => $this->comment,
            'notification' => $this->notification,
        ];
    }
}
