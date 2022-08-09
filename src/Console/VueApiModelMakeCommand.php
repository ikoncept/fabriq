<?php

namespace Ikoncept\Fabriq\Console;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;
use Infab\Core\Console\ReplacesModelName;
use InvalidArgumentException;
use Symfony\Component\Console\Input\InputOption;

class VueApiModelMakeCommand extends GeneratorCommand
{
    use ReplacesModelName;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'make:vue-api-model-template';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a Fabriq opionated javascript api model from stubs';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'VueApiModel';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $stub = parent::buildClass($name);

        $model = $this->argument('name');

        return $model ? $this->replaceModel($stub, $model) : $stub;
    }

    /**
     * Replace the model for the given stub.
     *
     * @param  string  $stub
     * @param  string  $model
     * @return string
     */
    protected function replaceModel($stub, $model)
    {
        $modelClass = $this->parseModel($model);

        $replace = [
            'DummyFullModelClass' => $modelClass,
            '{{ namespacedModel }}' => $modelClass,
            '{{namespacedModel}}' => $modelClass,
            'DummyModelClass' => class_basename($modelClass),
            '{{ model }}' => class_basename($modelClass),
            '{{model}}' => class_basename($modelClass),
            '{{ pluralModel }}' => Str::pluralStudly(class_basename($modelClass)),
            'DummyModelVariable' => lcfirst(class_basename($modelClass)),
            '{{ modelVariable }}' => lcfirst(class_basename($modelClass)),
            '{{modelVariable}}' => lcfirst(class_basename($modelClass)),
            '{{ pluralModelVariable }}' => Str::plural(lcfirst(class_basename($modelClass))),
            '{{ swedishPluralName }}' => Str::lower($this->option('swedish-name-plural')),
            '{{ swedishName }}' => Str::lower($this->option('swedish-name')),
            '{{ SwedishName }}' => Str::studly($this->option('swedish-name')),
            '{{ SwedishPluralName }}' => Str::studly($this->option('swedish-name-plural')),
        ];

        return str_replace(
            array_keys($replace),
            array_values($replace),
            $stub
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

    protected function alreadyExists($rawName)
    {
        $name = class_basename(str_replace('\\', '/', $rawName));

        $path = "{$this->laravel['path']}/../resources/js/models/{$name}.js";

        return file_exists($path);
    }

    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['swedish-name', 's', InputOption::VALUE_OPTIONAL, 'Swedish name'],
            ['swedish-name-plural', 'sp', InputOption::VALUE_OPTIONAL, 'Swedish plural name'],
        ];
    }

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/stubs/vue.api-model.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = class_basename(str_replace('\\', '/', $name));

        return "{$this->laravel['path']}/../resources/js/models/{$name}.js";
    }
}
