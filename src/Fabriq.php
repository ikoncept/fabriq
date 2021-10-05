<?php

namespace Ikoncept\Fabriq;

use Illuminate\Support\Facades\Route;
use InvalidArgumentException;
use Illuminate\Database\Eloquent\Model;

class Fabriq
{
     /**
     * Binds the Fabriq routes into the controller.
     *
     * @param  callable|null  $callback
     * @param  array  $options
     * @return void
     */
    public static function routes($callback = null, array $options = [])
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }

    /**
     * Return a new instance of a model
     *
     * @param string $key
     * @param mixed ...$arguments
     * @return mixed
     */
    public static function getModelClass(string $key, ...$arguments)
    {
        $class = config('fabriq.models.' . $key);
        if(! $class) {
            throw new InvalidArgumentException('The model key was not found: ' . $key);
        }

        return new $class($arguments);
    }

    public static function getFqnModel(string $key) : mixed
    {
        $class = config('fabriq.models.' . $key);
        if(! $class) {
            throw new InvalidArgumentException('The model key was not found: ' . $key);
        }

        return $class;
    }
}
