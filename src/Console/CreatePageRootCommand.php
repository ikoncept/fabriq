<?php

namespace Ikoncept\Fabriq\Console;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;

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
        $name = 'root';
        if (! $this->option('silent')) {
            $name = $this->ask('Provide a name for the page root', 'root');
        }

        if (Fabriq::getModelClass('page')->whereName('root')->first()) {
            $this->info('Root already exists');

            return 0;
        }

        $page = Fabriq::getModelClass('page');
        $page->name = $name;
        $page->template_id = 1;
        $page->save();

        $this->info('Page root has been created successfully');

        return 0;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['silent', 's', InputOption::VALUE_NONE, 'Generate a root named root'],
        ];
    }
}
