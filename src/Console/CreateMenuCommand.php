<?php

namespace Ikoncept\Fabriq\Console;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Console\Command;

class CreateMenuCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:create-menu';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new menu';

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
        $name = $this->ask("What is the new name of the menu?") ?: '';

        $slug = $this->ask("Provide a slug for the menu") ?: '';

        if(! $name) {
            $this->error('Name is required, exiting');
            return 1;
        }

        if(! $slug) {
            $this->error('Slug is required, exiting');
            return 1;
        }

        $menu = Fabriq::getModelClass('menu');
        $menu->name = $name;
        $menu->slug = $slug;
        $menu->save();

        $menuItem = Fabriq::getModelClass('menuItem');
        $menuItem->menu_id = $menu->id;
        $menuItem->save();

        $this->info('Menu created successfully created');
        $this->table(
            ['Name', 'Slug', 'id'],
            [collect($menu)->only(['id', 'name', 'slug'])->toArray()]
        );

        return 0;
    }
}
