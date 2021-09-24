<?php

namespace Ikoncept\Fabriq\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;

class VueResourceMakeCommand extends GeneratorCommand
{
    /**
     * The name of the console command.
     *
     * @var string
     */
    protected $name = 'make:fabriq-vue-resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Vue resource';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return $this->resolveStubPath('/stubs/model.pivot.stub');
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
            ['all', 'a', InputOption::VALUE_NONE, 'Generate an index, edit, and api model'],
            ['index', 'i', InputOption::VALUE_NONE, 'Create an index component'],
            ['edit', 'e', InputOption::VALUE_NONE, 'Create an edit component'],
            ['api-model', 'am', InputOption::VALUE_NONE, 'Create a new api model'],
        ];
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $this->swedishSingularName = $this->ask('Swedish singular name .eg "Artikel"');
        $this->swedishPluralName = $this->ask('Swedish plural name .eg "Artiklar"');


        if ($this->option('all')) {
            $this->input->setOption('index', true);
            $this->input->setOption('edit', true);
            $this->input->setOption('api-model', true);
        }

        if($this->option('index')) {
            $this->createIndexComponent();
        }

        if($this->option('edit')) {
            $this->createEditComponent();
        }

        if($this->option('api-model')) {
            $this->createApiModel();
        }


        if($this->option('index') || $this->option('edit')) {
            $this->info('Add below to your routes file');
            $this->info('Imports:');
        }
        if($this->option('index')) {
            $this->comment("import {$this->replaceModel($this->argument('name'), '{{ pluralModel }}')}Index from '~/{$this->replaceModel($this->argument('name'), '{{ pluralModelVariable }}')}/Index'");
        }
        if($this->option('edit')) {
            $this->comment("import {$this->replaceModel($this->argument('name'), '{{ pluralModel }}')}Edit from '~/{$this->replaceModel($this->argument('name'), '{{ pluralModelVariable }}')}/Edit'");
        }

        if($this->option('index') || $this->option('edit')) {
            $this->info('Routes:');
        }


        if($this->option('index')) {
            $this->comment('{');
            $this->comment("    path: '/{$this->replaceModel($this->argument('name'), '{{ pluralModelVariable }}')}',");
            $this->comment("    name: '{$this->replaceModel($this->argument('name'), '{{ pluralModelVariable }}')}.index',");
            $this->comment("    component: {$this->replaceModel($this->argument('name'), '{{ pluralModel }}')}Index,");
            $this->comment("    meta: {");
            $this->comment("        middleware: roles,");
            $this->comment("        roles: ['admin']");
            $this->comment("    }");
            $this->comment("},");
        }

        if($this->option('edit')) {
            $this->comment('{');
            $this->comment("    path: '/{$this->replaceModel($this->argument('name'), '{{ pluralModelVariable }}')}/:id/edit',");
            $this->comment("    name: '{$this->replaceModel($this->argument('name'), '{{ pluralModelVariable }}')}.edit',");
            $this->comment("    component: {$this->replaceModel($this->argument('name'), '{{ pluralModel }}')}Edit,");
            $this->comment("    meta: {");
            $this->comment("        middleware: roles,");
            $this->comment("        roles: ['admin']");
            $this->comment("    }");
            $this->comment("}");
        }

        return 0;
    }

    /**
     * Create a index component.
     *
     * @return void
     */
    protected function createIndexComponent()
    {
        $name = Str::studly($this->argument('name'));
        $this->call('make:vue-index-template', [
            'name' => $name,
            '--swedish-name' => $this->swedishSingularName,
            '--swedish-name-plural' => $this->swedishPluralName
        ]);
    }

    /**
     * Create a edit component.
     *
     * @return void
     */
    public function createEditComponent()
    {
        $name = Str::studly($this->argument('name'));
        $this->call('make:vue-edit-template', [
            'name' => $name,
            '--swedish-name' => $this->swedishSingularName,
            '--swedish-name-plural' => $this->swedishPluralName
        ]);
    }

    /**
     * Create a javscript api model.
     *
     * @return void
     */
    public function createApiModel()
    {
        $name = Str::studly($this->argument('name'));
        $this->call('make:vue-api-model-template', [
            'name' => $name
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

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $model
     * @return string
     */
    protected function replaceModel($model, $value)
    {
        $modelClass = $this->parseModel($model);

        $replace = [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{ pluralModel }}' => Str::pluralStudly(class_basename($modelClass)),
            '{{model}}' => class_basename($modelClass),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
            '{{ pluralModelVariable }}' => Str::plural(lcfirst(class_basename($modelClass))),
            // '{{ swedishPluralName }}' => Str::lower($this->option('swedish-name-plural')),
            // '{{ swedishName }}' => Str::lower($this->option('swedish-name')),
            // '{{ SwedishName }}' => Str::studly($this->option('swedish-name')),
            // '{{ SwedishPluralName }}' => Str::studly($this->option('swedish-name-plural')),
        ];


        return str_replace(
            array_keys($replace), array_values($replace), $value
        );
    }

    /**
     * Get the fully-qualified model class name.
     *
     * @param  string  $model
     * @return string
     *
     * @throws \InvalidArgumentException
     */
    protected function parseModel($model)
    {
        if (preg_match('([^A-Za-z0-9_/\\\\])', $model)) {
            throw new InvalidArgumentException('Model name contains invalid characters.');
        }

        return $this->qualifyModel($model);
    }

}
