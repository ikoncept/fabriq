
[![Latest Stable Version](http://poser.pugx.org/ikoncept/fabriq/v)](https://packagist.org/packages/ikoncept/fabriq)
[![tests](https://github.com/ikoncept/fabriq/actions/workflows/phpunit.yml/badge.svg)](https://github.com/ikoncept/fabriq/actions/workflows/phpunit.yml)
[![PHPStanLevel7](https://github.com/ikoncept/fabriq/actions/workflows/phpStan.yml/badge.svg)](https://github.com/ikoncept/fabriq/actions/workflows/phpStan.yml)
[![PHP Version Require](http://poser.pugx.org/ikoncept/fabriq/require/php)](https://packagist.org/packages/ikoncept/fabriq)




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
```
rm resources/js/app.js && rm resources/js/bootstrap.js
```


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
    'middleware' => ['auth:sanctum']
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
