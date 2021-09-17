<?php

namespace Ikoncept\Fabriq\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NotifyAboutNotification extends Notification
{
    use Queueable;

    /**
     * Notifications count
     *
     * @var int
     */
    public $count;


    /**
     * User
     *
     * @var mixed
     */
    public $user;

    /**
     *
     * @param integer $count
     * @param mixed $user
     */
    public function __construct(int $count, $user)
    {
        $this->count = $count;
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $countString = 'Du har en oläst notis i Fabriq CMS. Klicka på knappen nedan för att komma till notisen';
        if($this->count > 1) {
            $countString = 'Du har ' . $this->count . ' olästa notiser i Fabriq CMS. Klicka på knappen nedan för att läsa notiserna';
        }
        $appName = config('app.name');
        return (new MailMessage)
                    ->subject("({$this->count}) Olästa notiser i Fabriq CMS - {$appName}")
                    ->greeting("Hej {$this->user->firstName}!")
                    ->line($countString)
                    ->action('Visa notiser', url('/notifications'))
                    ->line('Det var allt!');
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
            //
        ];
    }
}
