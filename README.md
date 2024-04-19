[![Latest Stable Version](http://poser.pugx.org/ikoncept/fabriq/v)](https://packagist.org/packages/ikoncept/fabriq)
[![tests](https://github.com/ikoncept/fabriq/actions/workflows/phpunit.yml/badge.svg)](https://github.com/ikoncept/fabriq/actions/workflows/phpunit.yml)
[![PHPStanLevel7](https://github.com/ikoncept/fabriq/actions/workflows/phpStan.yml/badge.svg)](https://github.com/ikoncept/fabriq/actions/workflows/phpStan.yml)
[![PHP Version Require](http://poser.pugx.org/ikoncept/fabriq/require/php)](https://packagist.org/packages/ikoncept/fabriq)


![Fabriq CMS logo](https://media.fabriq-cms.se/public/fabriq-og-image-1200.jpg)

## Fabriq CMS

## Installation instructions 💻

```
composer require ikoncept/fabriq
```

If you're planning on using AWS s3:
```bash
# Laravel > 9
composer require --with-all-dependencies league/flysystem-aws-s3-v3 "^1.0"

# Laravel 9+
composer require league/flysystem-aws-s3-v3 "^3.0"
```

Install the Mailgun driver
```bash
composer require symfony/mailgun-mailer symfony/http-client
```


Install [Laravel Sanctum](https://github.com/laravel/sanctum) as well for authentication
```
composer require laravel/sanctum
```

Add the domain to the `.env` file:
```
SANCTUM_STATEFUL_DOMAINS=your-domain.test
SESSION_DOMAIN=your-domain.test
```

Publish the configurations:
```
php artisan vendor:publish --provider="Ikoncept\Fabriq\FabriqCoreServiceProvider" --tag=config
php artisan vendor:publish --provider="Infab\TranslatableRevisions\TranslatableRevisionsServiceProvider" --tag=config
```

Setup your database using the .env


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

Run the `fabriq:install` command:
```
php artisan fabriq:install
```
This command will publish front end assets and views. It will also run the migrations

**Important** Delete the files `app.js` and `bootstrap.js` in the `resources/js` directory
```
rm resources/js/app.js && rm resources/js/bootstrap.js
```


Run `pnpm install` and `pnpm production` to build assets
```
pnpm install && pnpm production
```

## Auth configuration 🗝

### Laravel below v11
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

### Laravel v11 and above

>[!NOTE]
> On Laravel 11 and up the this is not necessary since the files are overwritten when installing



## Register routes 🛣

>[!NOTE]
> On Laravel 11 and up the this is not necessary since the files are overwritten when installing

Register the routes that makes sense for your app. See below examples
```php
// routes/api.php
use Ikoncept\Fabriq\Fabriq;

Fabriq::routes(function ($router) {
    $router->forDevProtected();
}, [
    'middleware' => ['auth:sanctum', 'role:dev', 'verified'],
    'prefix' => 'dev'
]);

Fabriq::routes(function ($router) {
    $router->forApiAdminProtected();
}, [
    'middleware' => ['auth:sanctum', 'role:admin', 'verified'],
    'prefix' => 'admin'
]);

Fabriq::routes(function ($router) {
    $router->forApiProtected();
}, [
    'middleware' => ['auth:sanctum']
]);

Fabriq::routes(function ($router) {
    $router->forPublicApi();
});


```

```php
// routes/web.php

use Ikoncept\Fabriq\Fabriq;

Fabriq::routes(
    function ($router) {
        $router->allWeb();
    }
);
```


Create your first user in the database, or by using a package like [michaeldyrynda/laravel-make-user](https://github.com/michaeldyrynda/laravel-make-user)


#### Publishing assets 🗄️
Assets can be published using their respective tags. The tags that are available are:
* `config` - The config file
* `fabriq-translations` - Translations for auth views and validation messages
* `fabriq-frontend-assets` - Front end build system and Vue project files
* `fabriq-views` - Blade views and layouts

You can publish these assets using the command below:
```
php artisan vendor:publish --provider="Ikoncept\Fabriq\FabriqCoreServiceProvider" --tag=the-tag
```

If you want to overwrite your old published assets with new ones (for example when the package has updated views) you can use the `--force` flag
```
php artisan vendor:publish --provider="Ikoncept\Fabriq\FabriqCoreServiceProvider" --tag=fabriq-views --force
```

**Note** _Above tags have been published when the `fabriq:install` was run_

### Broadcasting 📢
Fabriq leverages [laravel/echo](https://github.com/laravel/echo) as a front end dependency to communicate with a pusher server. This package is preconfigured to use Ikoncept's own websocket server, but a pusher implementation can be swapped in.

To enable semi automatic prescense broadcasting go to the `/resources/js/plugins/index.js` and un-comment the the line for Laravel Echo:
```js
// import '~/plugins/laravel-echo'
import '~/plugins/toast'
import '~/plugins/v-calendar'
import '~/plugins/v-mask'
// ...
```

If the Laravel Echo plugin isn't imported it will not be enabled.

Don't forget to add the proper `.env` variables:

```
BROADCAST_DRIVER=ikoncept_pusher
PUSHER_APP_ID=400
PUSHER_APP_KEY=your-key
PUSHER_APP_SECRET=your-secret
PUSHER_APP_CLUSTER=mt1
```

If you want to have a presence channel for a specific page, simply add it to the route:
```js
    {
        path: '/articles/:id/edit',
        name: 'articles.edit',
        component: ArticlesEdit,
        meta: {
            middleware: [RolesMiddleware, PresenceMiddleware], // <- Added here (PresenceMiddleware)
            roles: ['admin'],
        }
    },
```

If you want to have a broadcast channel for a specific page, simply add it to the route:
```js
    {
        path: '/articles/:id/edit',
        name: 'articles.edit',
        component: ArticlesEdit,
        meta: {
            middleware: [RolesMiddleware, BroadcastMiddleware], // <- Added here (PresenceMiddleware)
            roles: ['admin'],
            broadcastName: 'article'
        }
    },
```
When the broadcast middleware is applied it will listen to `updated`, `created` and `deleted` events. Which is useful for index views when live updates are needed.



### Updating ♻️
You can publish new front end assets with the `php artisan fabriq:update` command. This command will publish new front end assets and run migrations.

### Done? 🎉
That should be it, serve the app and login at `/login`

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

## Testing

```
composer test
```
