<?php

namespace Ikoncept\Fabriq\Console\Commands;
use Illuminate\Support\Str;
use Illuminate\Console\GeneratorCommand;
use Infab\Core\Console\ReplacesModelName;
use Symfony\Component\Console\Input\InputOption;

class PublishControllerCommand extends GeneratorCommand
{
    use ReplacesModelName;

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'fabriq:publish-controller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Publishes a controller from a stub';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'ApiController';

    /**
     * Build the class with the given name.
     *
     * @param  string  $name
     * @return string
     */
    protected function buildClass($name)
    {
        $this->type = $name;
        $stub = $this->replaceUserNamespace(
            parent::buildClass($name)
        );
        $stub = $this->files->get($this->getStub());

        $model = $this->option('model') ?? null;
        $stub = $this->replaceNamespace($stub, $name)->replaceClass($stub, $name);

        // if ($this->option('tests')) {
        //     $this->createTests($model);
        // }


        return $model ? $this->replaceModel($stub, $model) : $stub;
    }

    /**
     * Replace the User model namespace.
     *
     * @param  string  $stub
     * @return string
     */
    protected function replaceUserNamespace($stub)
    {
        return str_replace(
            $this->rootNamespace().'User',
            config('auth.providers.users.model'),
            $stub
        );
    }


    /**
     * Get the console command arguments.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['model', 'm', InputOption::VALUE_OPTIONAL, 'The model that the Api Controller applies to.'],
            ['stub', 's', InputOption::VALUE_REQUIRED, 'The stub to be used.'],
            ['tests', 't', InputOption::VALUE_OPTIONAL, 'Create basic tests for the created API Controller.'],
        ];
    }


    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__ . '/../../../stubs/' . $this->option('stub');
    }

    /**
     * Get the default namespace for the class.
     *
     * @param  string  $rootNamespace
     * @return string
     */
    protected function getDefaultNamespace($rootNamespace)
    {
        return $rootNamespace.'\Http\Controllers\Api';
    }

    /**
     * Create basic tests for the api endpoints
     *
     * @param  string  $rootNamespace
     * @return string
     */
    // protected function createTests($model)
    // {
    //     $name = str_replace('Controller', '', $this->getNameInput());
    //     $this->call('make:api-controller-tests', [
    //         'name' => "{$name}FeatureTest",
    //         '--model' => $model,
    //     ]);
    // }

}
