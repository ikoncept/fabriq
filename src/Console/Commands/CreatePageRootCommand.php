<?php

namespace Ikoncept\Fabriq\Console\Commands;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Console\Command;

class CreatePageRootCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fabriq:create-page-root';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a page root';

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
        $name = $this->ask('Provide a name for the page root', 'root');
        $page = Fabriq::getModelClass('pages');
        $page->name = $name;
        $page->template_id = 1;
        $page->save();

        $this->info('Page root has been created successfully');
    }
}
