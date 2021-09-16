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
     * Register routes for transient tokens, clients, and personal access tokens.
     *
     * @return void
     */
    public function all() : void
    {
        $this->forArticles();
    }

    public function allInternal() : void
    {
        $this->forInternalMiscRoutes();

        $this->forInternalArticles();
        $this->forInternalContacts();
        $this->forInternalBlockTypes();
        $this->forInternalComments();
        $this->forInternalConfig();
        $this->forInternalEvents();
        $this->forInternalFiles();
        $this->forInternalImages();
        $this->forInternalDownloads();
        $this->forInternalMenus();
        $this->forInternalPages();
        $this->forInternalRoles();
        $this->forInternalSmartBlocks();
        $this->forInternalTags();
        $this->forInternalUsers();
        $this->forInternalVideos();
    }

    /**
     * Register the routes needed for authorization.
     *
     * @return void
     */
    public function forArticles() : void
    {
        Route::get('articles', [\App\Http\Controllers\Api\Fabriq\ArticlesController::class, 'index']);
        Route::post('articles', [\App\Http\Controllers\Api\Fabriq\ArticlesController::class, 'store']);
        Route::get('articles/{id}', [\App\Http\Controllers\Api\Fabriq\ArticlesController::class, 'show']);
        Route::patch('articles/{id}', [\App\Http\Controllers\Api\Fabriq\ArticlesController::class, 'update']);
        Route::delete('articles/{id}', [\App\Http\Controllers\Api\Fabriq\ArticlesController::class, 'destroy']);
    }

    public function forInternalArticles() : void
    {
        Route::resource('articles', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ArticlesController::class);
    }

    public function forInternalContacts() : void
    {
        Route::resource('contacts', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ContactsController::class);
    }

    public function forInternalBlockTypes() : void
    {
        Route::resource('block-types', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\BlockTypesController::class);
    }

    public function forInternalComments() : void
    {
        Route::get('{model}/{id}/comments', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentableController::class, 'index']);
        Route::post('{model}/{id}/comments', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentableController::class, 'store']);
        Route::patch('comments/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentsController::class, 'update']);
        Route::delete('comments/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentsController::class, 'destroy']);
    }

    public function forInternalConfig() : void
    {
        Route::get('config', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ConfigController::class, 'index']);
    }

    public function forInternalEvents() : void
    {
        Route::resource('events', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\EventsController::class);
    }

    public function forInternalFiles() : void
    {
        Route::get('files', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'index']);
        Route::get('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'show']);
        Route::patch('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'update']);
        Route::delete('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FilesController::class, 'destroy']);
    }

    public function forInternalImages() : void
    {
        Route::get('/{model}/{id}/images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageablesController::class, 'index']);
        Route::post('/images/{id}/{model}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageablesController::class, 'store']);
        Route::get('images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'index']);
        Route::get('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'show']);
        Route::patch('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'update']);
        Route::delete('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImagesController::class, 'destroy']);
    }

    public function forInternalDownloads() : void
    {
        Route::get('downloads', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\DownloadsController::class, 'index']);
    }

    public function forInternalMiscRoutes() : void
    {
        Route::get('templates', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\RevisionTemplatesController::class, 'index']);
        Route::get('menus/{slug}/public', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemTreeController::class, 'show']);
        Route::get('{model}/count', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ModelCountController::class, 'show']);

        // Uploads
        Route::post('uploads/images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageUploadsController::class, 'store']);
        Route::post('uploads/files', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FileUploadsController::class, 'store']);
        Route::post('uploads/videos', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideoUploadsController::class, 'store']);
    }

    public function forInternalMenus() : void
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

    public function forInternalPages() : void
    {
        Route::get('pages-tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageTreeController::class, 'index']);
        Route::patch('pages-tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageTreeController::class, 'update']);
        Route::get('pages/{slug}/live', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSlugsController::class, 'show']);
        Route::get('pages/{slug}/preview', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSlugPreviewsController::class, 'show'])->name('pages.show.preview');
        Route::get('pages', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'index']);
        Route::post('pages', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'store']);
        Route::get('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'show']);
        Route::patch('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'update']);
        Route::delete('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagesController::class, 'destroy']);
        Route::post('pages/{id}/publish', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PublishPagesController::class, 'store']);
        Route::get('pages/{id}/signed-url', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSignedUrlsController::class, 'show']);
    }

    public function forInternalRoles() : void
    {
        Route::get('roles', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\RolesController::class, 'index']);
    }

    public function forInternalSmartBlocks() : void
    {
        Route::resource('smart-blocks', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\SmartBlocksController::class);
    }

    public function forInternalTags() : void
    {
        Route::get('tags', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\TagsController::class, 'index']);
        Route::post('tags', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\TagsController::class, 'store']);
    }

    public function forInternalUsers() : void
    {
        Route::get('user', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'index']);
        Route::patch('user', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'update']);
        Route::post('user/send-email-verification', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\EmailVerificationsController::class, 'store']);
        Route::get('/user/notifications', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\NotificationsController::class, 'index']);
        Route::patch('/user/notifications/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\NotificationsController::class, 'update']);

        Route::resource('users', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\UsersController::class);
    }

    public function forInternalVideos() : void
    {
        Route::get('videos', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'index']);
        Route::get('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'show']);
        Route::patch('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'update']);
        Route::delete('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideosController::class, 'destroy']);
    }
}
