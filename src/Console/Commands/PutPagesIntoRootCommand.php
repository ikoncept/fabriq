<?php

namespace Ikoncept\Fabriq\Console\Commands;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Console\Command;

class PutPagesIntoRootCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pages:move-into-root';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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
        config('fabriq.models.page')::fixTree();
        $root = Fabriq::getModelClass('page');
        $root->template_id = 1;
        $root->name = 'root';
        $root->save();
        config('fabriq.models.page')::fixTree();
        $pages = config('fabriq.models.page')::where('id', '!=', $root->id)->get();

        foreach($pages as $page) {
            $page->appendToNode($root)->save();
        }
        return 0;
    }
}
