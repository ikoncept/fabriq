<?php

namespace Ikoncept\Fabriq\Console;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Notifications\NotifyAboutNotification;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class SendNotificationReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:send-notification-reminders';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Notifies about @mentions once';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $users = Fabriq::getModelClass('user')->whereHas('fabriqNotifications', function (Builder $query) {
            return $query->whereNull('cleared_at')
                ->whereNull('notified_at');
        })->withCount('notificationsToBeNotified')->with('notificationsToBeNotified')->get();

        foreach ($users as $user) {
            $user->notify(new NotifyAboutNotification($user->notificationsToBeNotified->count(), $user));
            $this->clearNotifications($user->notificationsToBeNotified);
            $this->info('Sending notification to '.$user->email);
        }

        return 0;
    }

    protected function clearNotifications(Collection $notifications): void
    {
        $notifications->each(function ($item) {
            $item->notified_at = now();
            $item->save();
        });
    }
}
