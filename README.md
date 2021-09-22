

[![tests](https://github.com/ikoncept/fabriq/workflows/tests/badge.svg?branch=main)](https://github.com/ikoncept/fabriq/actions?query=workflow%3Atests)
[![PHPStanLevel7](https://github.com/ikoncept/fabriq/workflows/PHPStanLevel7/badge.svg?branch=main)](https://github.com/ikoncept/fabriq/actions?query=workflow%3APHPStanLevel7)
[![minimum_php_version](https://img.shields.io/badge/php-%5E7.3%7C%5E8.0-%238892BF)](https://gitlab.com/ikoncept/fabriq/-/commits/main)



## Fabriq CMS

#### Dependencies and requirements
    spatie/laravel-permission
    infab/core
    spatie/laravel-query-builder,
    spatie/laravel-medialibrary,
    kalnoy/nestedset,
    spatie/laravel-tags
    spatie/laravel-sluggable
    infab/translatable-revisions
    laravel/fortify

## Installation instructions 💻

```
composer require ikoncept/fabriq
```

You probably want to install [Laravel Sanctum](https://github.com/laravel/sanctum) as well for authentication
```
composer require laravel/sanctum
```

Setup your database using the .env

Run the `fabriq:install` command:
```
$ php artisan fabriq:install
```
This command will publish front end assets and views. It will also run the migrations

**Important** Delete the files `app.js` and `bootstrap.js` in the `resources/js` directory


Run `yarn install` and `yarn production` to build assets
```
$ yarn install && yarn production
```

## Auth configuration 🗝
Enable the Laravel Sanctum middleware in `app\Http\Kernel.php`
```php
    // app\Http\Kernel.php

    /**
     * The application's route middleware groups.
     *
     * @var array
     */
    protected $middlewareGroups = [
        'web' => [
            \App\Http\Middleware\EncryptCookies::class,
            \Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse::class,
            \Illuminate\Session\Middleware\StartSession::class,
            // \Illuminate\Session\Middleware\AuthenticateSession::class,
            \Illuminate\View\Middleware\ShareErrorsFromSession::class,
            \App\Http\Middleware\VerifyCsrfToken::class,
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],

        'api' => [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class, // <---
            'throttle:api',
            \Illuminate\Routing\Middleware\SubstituteBindings::class,
        ],
    ];

```

## Modify the user model 🧘

The user model need to extend the Fabriq\Models\User::class

```php
// app/Models/User.php

//...
use Ikoncept\Fabriq\Models\User as FabriqUser;
//...

class User extends FabriqUser

// ...
```

## Register routes 🛣
Register the routes that makes sense for your app. See below examples
```php
// routes/api.php

Fabriq::routes(function($router) {
    $router->forApiAdminProtected();
},[
    'middleware' => ['auth:sanctum', 'role:admin', 'verified'],
    'prefix' => 'admin'
]);

Fabriq::routes(function($router) {
    $router->forApiProtected();
},[
    'middleware' => ['auth:sanctum'],
    'prefix' => 'admin'
]);

Fabriq::routes(function($router) {
    $router->forApiProtected();
},[
    'middleware' => ['auth:sanctum', 'role:dev', 'verified'],
    'prefix' => 'dev'
]);

```

```php
// routes/web.php

use Ikoncept\Fabriq;

Fabriq::routes(
    function ($router) {
        $router->allWeb();
    }
);
```


Create your first user in the database, or by using a package like [michaeldyrynda/laravel-make-user](https://github.com/michaeldyrynda/laravel-make-user)


#### Publishing assets 🗄️
Assets can be published using their respective tags. The tags that are available are:
* `fabriq-config` - The config file
* `fabriq-translations` - Translations for auth views and validation messages
* `fabriq-frontend-assets` - Front end build system and Vue project files
* `fabriq-views` - Blade views and layouts

You can publish these assets using the command below:
```
$ php artisan vendor:publish --provider="Ikoncept\Fabriq\FabriqCoreServiceProvider" --tag=the-tag
```

If you want to overwrite your old published assets with new ones (for example when the package has updated views) you can use the `--force` flag
```
$ php artisan vendor:publish --provider="Ikoncept\Fabriq\FabriqCoreServiceProvider" --tag=fabriq-views --force
```

**Note** _Above tags have been published when the `fabriq:install` was run_

### Done? 🎉
That should be it, serve the app and login at `/login`

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Testing

```
$ composer test
```
