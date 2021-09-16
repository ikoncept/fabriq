<?php

namespace Ikoncept\Fabriq;

use App\Http\Controllers\Api\Fabriq\ArticlesController;
use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\Facades\Route;

class RouteRegistrar
{
    /**
     * The router implementation.
     *
     * @var \Illuminate\Contracts\Routing\Registrar
     */
    protected $router;

    /**
     * Create a new route registrar instance.
     *
     * @param  \Illuminate\Contracts\Routing\Registrar  $router
     * @return void
     */
    public function __construct(Router $router)
    {
        $this->router = $router;
    }

    /**
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all()
    {
        $this->forArticles();
    }

    /**
     * Register the routes needed for authorization.
     *
     * @return void
     */
    public function forArticles()
    {
        Route::get('articles', [ArticlesController::class, 'index']);
        Route::post('articles', [ArticlesController::class, 'store']);
        Route::get('articles/{id}', [ArticlesController::class, 'show']);
        Route::patch('articles/{id}', [ArticlesController::class, 'update']);
        Route::delete('articles/{id}', [ArticlesController::class, 'destroy']);
    }

    public function forInternalArticles()
    {
        Route::resource('articles', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ArticlesController::class);
    }
}
