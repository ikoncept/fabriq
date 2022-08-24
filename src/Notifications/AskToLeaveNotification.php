<?php

namespace Ikoncept\Fabriq\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class AskToLeaveNotification extends Notification
{
    use Queueable;

    /**
     * @var Model
     */
    public $causer;

    /**
     * @var string
     */
    public $pageIdentifier;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Model $causer, string $pageIdentifier)
    {
        $this->causer = $causer;
        $this->pageIdentifier = $pageIdentifier;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['broadcast'];
    }

    /**
     * Get the broadcastable representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return BroadcastMessage
     */
    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'causer' => $this->causer,
            'timeout' => 10,
            'can_decline' => true,
            'identifier' => $this->pageIdentifier,
            'text' => $this->causer->first_name.' vill redigera denna sidan. ',
        ]);
    }

    /**
     * Get the type of the notification being broadcast.
     *
     * @return string
     */
    public function broadcastType()
    {
        return 'broadcast.ask-to-leave';
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
        ];
    }
}
