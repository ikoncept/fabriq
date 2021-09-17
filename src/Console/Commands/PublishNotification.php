<?php

namespace Ikoncept\Fabriq\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;

class PublishNotification extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:publish-notification';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes a notification for all registered admins';

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
        $notificationText =  $this->ask('Enter the message you want to publish to the admins');

        $this->info('The message:');
        $this->info($notificationText);
        $confirmation = $this->confirm('Are you sure you wan\'t to publish this message?');

        if(! $confirmation) {
            $this->error('Aborted!');
            return 0;
        }

        $admins = config('fabriq.models.notification')::whereHas('roles', function(Builder $query) {
            return $query->where('name', 'admin');
        })->get()->each(function($user) use ($notificationText) {
            config('fabriq.models.notification')::create([
                'user_id' => $user->id,
                'content' => $notificationText
            ]);
        });


        return 0;
    }
}
