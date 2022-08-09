<?php

namespace Ikoncept\Fabriq\Console;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

class AddRoleToUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:add-role-to-user {--id= : User id to add roles to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add role to the specified user';

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
        $roleOptions = Fabriq::getModelClass('role')->all();

        $id = $this->option('id');

        if (! $id) {
            $id = Fabriq::getModelClass('user')
                ->orderBy('created_at', 'desc')
                ->first()->id;
        }

        $user = Fabriq::getModelClass('user')
            ->where('id', $id)
            ->firstOrFail();

        $choices = $roleOptions->map(function ($item) {
            return $item->name.' | id: '.$item->id;
        });

        $role = $this->choice('Which role do you want to attach?', $choices->toArray());

        if (! preg_match_all('/id: ([\d]+)/', $role, $matches)) {
            $this->error('Failed to extract id, exiting');

            return 1;
        }

        $roleToBeAssigned = Fabriq::getModelClass('role')
            ->select('name')
            ->find($matches[1][0]);

        $user->assignRole($roleToBeAssigned->name);
        $user->email_verified_at = now();
        $user->save();

        $this->info('Assinged role '.$roleToBeAssigned->name.' to '.$user->email);

        return 0;
    }
}
