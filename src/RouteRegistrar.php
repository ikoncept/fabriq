<?php

namespace Ikoncept\Fabriq;


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
     * Register routes for web.
     *
     * @return void
     */
    public function allWeb() : void
    {
        Route::get('/email/verify', function ($request) {
            return view('auth.verify-email', ['request' => $request]);
        })->middleware('auth')->name('verification.notice');

        Route::get('/email/verify/{id}/{hash}', function (\Illuminate\Foundation\Auth\EmailVerificationRequest $request) {
            $request->fulfill();

            return redirect('/profile/settings');
        })->middleware(['auth', 'signed'])->name('verification.verify');

        Route::get('/email/verification-notification', function () {
            config('fabriq.models.user')::find(1)->sendEmailVerificationNotification();

            return 'ok';
            return back()->with('message', 'Verification link sent!');
        })->middleware(['auth', 'throttle:6,1'])->name('verification.send');

        // Route::get('login/infab',  [InfabAuthController::class, 'redirectToProvider']);
        // Route::get('login/infab/callback', [InfabAuthController::class, 'handleProviderCallback']);

        Route::get('/', [\Ikoncept\Fabriq\Http\Controllers\SpaController::class, 'index'])->middleware('auth');
        Route::get('/{any}', [\Ikoncept\Fabriq\Http\Controllers\SpaController::class, 'index'])->where('any', '.*')->middleware('auth');
    }

    /**
     * Register routes forPublic API endpoints.
     *
     * @return void
     */
    public function all() : void
    {
        $this->forMiscRoutes();
        $this->forArticles();
        $this->forContacts();
        $this->forBlockTypes();
        $this->forComments();
        $this->forEvents();
        $this->forFiles();
        $this->forImages();
        $this->forDownloads();
        $this->forMenus();
        $this->forPages();
        $this->forRoles();
        $this->forSmartBlocks();
        $this->forTags();
        $this->forUsers();
        $this->forVideos();
        $this->forNotifications();
        $this->forAuthenticatedUsers();
        $this->forConfig();
        $this->forPageSlugs();
    }

    public function forApiProtected()
    {
        $this->forNotifications();
        $this->forAuthenticatedUsers();
        $this->forConfig();
    }

    public function forApiAdminProtected()
    {
        $this->forMiscRoutes();
        $this->forArticles();
        $this->forContacts();
        $this->forBlockTypes();
        $this->forComments();
        $this->forEvents();
        $this->forFiles();
        $this->forImages();
        $this->forDownloads();
        $this->forMenus();
        $this->forPages();
        $this->forRoles();
        $this->forSmartBlocks();
        $this->forTags();
        $this->forUsers();
        $this->forVideos();
    }

    public function forPublicApi()
    {
        $this->forPageSlugs();
        $this->forImageSrcSet();
    }

    public function forDevProtected()
    {
        Route::post('bust-cache', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\BustCacheController::class, 'store']);
    }

    public function forArticles() : void
    {
        Route::resource('articles', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ArticlesController::class);
    }

    public function forContacts() : void
    {
        Route::resource('contacts', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ContactsController::class);
    }

    public function forBlockTypes() : void
    {
        Route::resource('block-types', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\BlockTypesController::class);
    }

    public function forComments() : void
    {
        Route::get('{model}/{id}/comments', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentableController::class, 'index']);
        Route::post('{model}/{id}/comments', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentableController::class, 'store']);
        Route::patch('comments/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentsController::class, 'update']);
        Route::delete('comments/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentsController::class, 'destroy']);
    }

    public function forConfig() : void
    {
        Route::get('config', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ConfigController::class, 'index']);
    }

    public function forEvents() : void
    {
        Route::resource('events', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\EventsController::class);
    }

    public function forFiles() : void
    {
        Route::get('files', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'index']);
        Route::get('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'show']);
        Route::patch('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'update']);
        Route::delete('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'destroy']);
    }

    public function forImages() : void
    {
        Route::get('images/{id}/src-set', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageSourceSetController::class, 'show']);
        Route::get('/{model}/{id}/images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageablesController::class, 'index']);
        Route::post('/images/{id}/{model}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageablesController::class, 'store']);
        Route::get('images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'index']);
        Route::get('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'show']);
        Route::patch('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'update']);
        Route::delete('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'destroy']);
    }

    public function forDownloads() : void
    {
        Route::get('downloads', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\DownloadsController::class, 'index']);
        Route::get('downloads/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\DownloadsController::class, 'show']);
    }

    public function forMiscRoutes() : void
    {
        Route::get('templates', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\RevisionTemplatesController::class, 'index']);
        Route::get('menus/{slug}/public', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemTreeController::class, 'show']);
        Route::get('{model}/count', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ModelCountController::class, 'show']);

        // Uploads
        Route::post('uploads/images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageUploadsController::class, 'store']);
        Route::post('uploads/files', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FileUploadsController::class, 'store']);
        Route::post('uploads/videos', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideoUploadsController::class, 'store']);
    }

    public function forMenus() : void
    {
        Route::get('menus', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenusController::class, 'index']);
        Route::post('menus', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenusController::class, 'store']);
        Route::get('menus/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenusController::class, 'show']);
        Route::patch('menus/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenusController::class, 'update']);
        Route::delete('menus/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenusController::class, 'destroy']);
        Route::get('menus/{id}/items/tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemTreeController::class, 'index']);
        Route::patch('menus/{id}/items/tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemTreeController::class, 'update']);
        Route::post('/menus/{id}/items', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemsController::class, 'store']);

        Route::get('menu-items/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemsController::class, 'show']);
        Route::patch('menu-items/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemsController::class, 'update']);
        Route::delete('menu-items/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemsController::class, 'destroy']);
    }

    public function forPages() : void
    {
        Route::get('pages-tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageTreeController::class, 'index']);
        Route::patch('pages-tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageTreeController::class, 'update']);
        Route::get('pages/{slug}/live', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSlugsController::class, 'show']);
        Route::get('pages', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'index']);
        Route::post('pages', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'store']);
        Route::get('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'show']);
        Route::patch('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'update']);
        Route::delete('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'destroy']);
        Route::post('pages/{id}/publish', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PublishPagesController::class, 'store']);
        Route::get('pages/{id}/signed-url', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSignedUrlsController::class, 'show']);
    }

    public function forPageSlugs()
    {
        Route::get('pages/{slug}/preview', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSlugPreviewsController::class, 'show'])->name('pages.show.preview');
    }

    public function forRoles() : void
    {
        Route::get('roles', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\RolesController::class, 'index']);
    }

    public function forSmartBlocks() : void
    {
        Route::resource('smart-blocks', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\SmartBlocksController::class);
    }

    public function forTags() : void
    {
        Route::get('tags', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\TagsController::class, 'index']);
        Route::post('tags', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\TagsController::class, 'store']);
    }

    public function forAuthenticatedUsers() : void
    {
        Route::get('user', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'index']);
        Route::patch('user', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'update']);
        Route::patch('user/self', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'update']);
        Route::post('user/send-email-verification', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\EmailVerificationsController::class, 'store']);
    }

    public function forUsers()
    {
        Route::resource('users', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\UsersController::class);
    }

    public function forNotifications()
    {
        Route::get('/user/notifications', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\NotificationsController::class, 'index']);
        Route::patch('/user/notifications/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\NotificationsController::class, 'update']);
    }

    public function forVideos() : void
    {
        Route::get('videos', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'index']);
        Route::get('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'show']);
        Route::patch('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'update']);
        Route::delete('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'destroy']);
    }

    public function forImageSrcSet() : void
    {
        Route::get('images/{id}/src-set', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageSourceSetController::class, 'show']);
    }
}
