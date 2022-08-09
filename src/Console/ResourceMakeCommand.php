<?php

namespace Ikoncept\Fabriq\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class ResourceMakeCommand extends GeneratorCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'make:fabriq-resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a model, controller, transformer and request classes';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->option('pivot')
                    ? $this->resolveStubPath('/stubs/model.pivot.stub')
                    : $this->resolveStubPath('/stubs/model.stub');
    }

    /**
     * Resolve the fully-qualified path to the stub.
     *
     * @param  string  $stub
     * @return string
     */
    protected function resolveStubPath($stub)
    {
        return file_exists($customPath = $this->laravel->basePath(trim($stub, '/')))
                        ? $customPath
                        : __DIR__.$stub;
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getArguments()
    {
        return [
            ['name', InputArgument::REQUIRED, 'The name of the resource'],
        ];
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['all', 'a', InputOption::VALUE_NONE, 'Generate a model, migration, factory, controller, transformer and requests'],
            ['model', 'm', InputOption::VALUE_NONE, 'The model that the resource applies to.'],
            ['factory', 'f', InputOption::VALUE_NONE, 'Create a new factory for the model'],
            ['migration', 'mi', InputOption::VALUE_NONE, 'Create a new migration file for the model'],
            ['transformer', 't', InputOption::VALUE_NONE, 'Create transformer'],
            ['requests', 'r', InputOption::VALUE_NONE, 'Create update and create requests'],
            ['controller', 'c', InputOption::VALUE_NONE, 'Create a new controller'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->option('all')) {
            $this->input->setOption('model', true);
            $this->input->setOption('factory', true);
            $this->input->setOption('migration', true);
            $this->input->setOption('transformer', true);
            $this->input->setOption('controller', true);
            $this->input->setOption('requests', true);
        }

        $this->createModel();

        if ($this->option('transformer')) {
            $this->createTransformer();
        }

        if ($this->option('controller')) {
            $this->createController();
        }

        if ($this->option('requests')) {
            $this->createRequests();
        }

        // if ($this->option('controller') || $this->option('resource') || $this->option('api')) {
        //     $this->createController();
        // }
        return 0;
    }

    /**
     * Create a controller for the model.
     *
     * @return void
     */
    protected function createController()
    {
        $model = Str::studly($this->argument('name'));

        $this->call('make:fabriq-controller', [
            'name' => "{$model}Controller",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }

    protected function createRequests()
    {
        $model = Str::studly($this->argument('name'));

        $this->call('make:request', [
            'name' => "Update{$model}Request",
        ]);
        $this->call('make:request', [
            'name' => "Create{$model}Request",
        ]);
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return is_dir(app_path('Models')) ? $rootNamespace.'\\Models' : $rootNamespace;
    }

    /**
     * Create a model factory for the model.
     *
     * @return void
     */
    protected function createModel()
    {
        $model = Str::studly($this->argument('name'));

        $this->call('make:model', [
            'name' => "{$model}",
            '--factory' => $this->option('factory'),
            '--migration' => $this->option('migration'),
        ]);
    }

    protected function createTransformer()
    {
        $model = Str::studly($this->argument('name'));

        $this->call('make:fabriq-transformer', [
            'name' => "{$model}Transformer",
            '--model' => $this->qualifyClass($this->getNameInput()),
        ]);
    }
}
