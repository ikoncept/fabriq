<?php

namespace Ikoncept\Fabriq;

use Illuminate\Contracts\Routing\Registrar as Router;
use Illuminate\Support\Facades\Broadcast;
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


        Route::get('/invitations/accept/{token}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AcceptInvitationController::class, 'show'])->name('invitation.accept');
        Route::post('/invitations/accept/{token}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AcceptInvitationController::class, 'store'])->name('invitation.accept.store');

        Route::get('/permalink/{hash}/{locale?}', \Ikoncept\Fabriq\Http\Controllers\PermaLinkRedirectController::class)
            ->name('permalink.redirect');

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
        $this->forPagePaths();
        $this->forInvitations();
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
        $this->forPagePaths();
        $this->forInvitations();
    }

    public function forPublicApi()
    {
        $this->forPageSlugs();
        $this->forImageSrcSet();
        // Route::post('logout', [AuthenticatedSessionController::class, 'destroy']);
    }

    public function forDevProtected()
    {
        Route::post('bust-cache', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\BustCacheController::class, 'store']);
    }

    public function forArticles() : void
    {
        Route::resource('articles', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ArticleController::class);
    }

    public function forContacts() : void
    {
        Route::resource('contacts', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ContactController::class);
    }

    public function forBlockTypes() : void
    {
        Route::resource('block-types', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\BlockTypeController::class);
    }

    public function forComments() : void
    {
        Route::get('{model}/{id}/comments', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentableController::class, 'index']);
        Route::post('{model}/{id}/comments', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentableController::class, 'store']);
        Route::patch('comments/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentController::class, 'update']);
        Route::delete('comments/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\CommentController::class, 'destroy']);
    }

    public function forConfig() : void
    {
        Route::get('config', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ConfigController::class, 'index']);
    }

    public function forEvents() : void
    {
        Route::resource('events', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\EventController::class);
    }

    public function forFiles() : void
    {
        Route::get('files', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FileController::class, 'index']);
        Route::get('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FileController::class, 'show']);
        Route::patch('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FileController::class, 'update']);
        Route::delete('files/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FileController::class, 'destroy']);
    }

    public function forImages() : void
    {
        Route::get('images/{id}/src-set', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageSourceSetController::class, 'show']);
        Route::get('/{model}/{id}/images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageableController::class, 'index']);
        Route::post('/images/{id}/{model}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageableController::class, 'store']);
        Route::get('images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageController::class, 'index']);
        Route::get('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageController::class, 'show']);
        Route::patch('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageController::class, 'update']);
        Route::delete('images/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageController::class, 'destroy']);
    }

    public function forDownloads() : void
    {
        Route::get('downloads', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\DownloadController::class, 'index']);
        Route::get('downloads/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\DownloadController::class, 'show']);
    }

    public function forMiscRoutes() : void
    {
        Route::get('templates', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\RevisionTemplateController::class, 'index']);
        Route::get('menus/{slug}/public', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemTreeController::class, 'show']);
        Route::get('{model}/count', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ModelCountController::class, 'show']);

        // Uploads
        Route::post('uploads/images', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageUploadController::class, 'store']);
        Route::post('uploads/files', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\FileUploadController::class, 'store']);
        Route::post('uploads/videos', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideoUploadController::class, 'store']);
    }

    public function forMenus() : void
    {
        Route::get('menus', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuController::class, 'index']);
        Route::post('menus', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuController::class, 'store']);
        Route::get('menus/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuController::class, 'show']);
        Route::patch('menus/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuController::class, 'update']);
        Route::delete('menus/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuController::class, 'destroy']);
        Route::get('menus/{id}/items/tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemTreeController::class, 'index']);
        Route::patch('menus/{id}/items/tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemTreeController::class, 'update']);
        Route::post('/menus/{id}/items', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemController::class, 'store']);

        Route::get('menu-items/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemController::class, 'show']);
        Route::patch('menu-items/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemController::class, 'update']);
        Route::delete('menu-items/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\MenuItemController::class, 'destroy']);
    }

    public function forPages() : void
    {
        Route::get('pages-tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageTreeController::class, 'index']);
        Route::patch('pages-tree', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageTreeController::class, 'update']);
        Route::get('pages/{slug}/live', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSlugsController::class, 'show']);
        Route::get('pages', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageController::class, 'index']);
        Route::post('pages', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageController::class, 'store']);
        Route::get('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageController::class, 'show']);
        Route::patch('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageController::class, 'update']);
        Route::delete('pages/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageController::class, 'destroy']);
        Route::post('pages/{id}/publish', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PublishPageController::class, 'store']);
        Route::get('pages/{id}/signed-url', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSignedUrlController::class, 'show']);
    }

    public function forInvitations() : void
    {
        // Route::get('/invitations/accept/{token}', [Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AcceptInvitationController::class, 'show'])->name('invitation.accept');
        Route::post('invitations/{userId}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\InvitationController::class, 'store'])->name('invitations.store');
        Route::delete('invitations/{userId}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\InvitationController::class, 'destroy'])->name('invitations.destroy');
    }

    public function forPageSlugs()
    {
        Route::get('pages/{slug}/preview', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PageSlugPreviewController::class, 'show'])->name('pages.show.preview');
    }

    public function forRoles() : void
    {
        Route::get('roles', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\RoleController::class, 'index']);
    }

    public function forSmartBlocks() : void
    {
        Route::resource('smart-blocks', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\SmartBlockController::class);
    }

    public function forTags() : void
    {
        Route::get('tags', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\TagController::class, 'index']);
        Route::post('tags', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\TagController::class, 'store']);
    }

    public function forAuthenticatedUsers() : void
    {
        Route::get('user', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'index']);
        Route::patch('user', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'update']);
        Route::post('user/image', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\UserImageController::class, 'store'])->name('user.image.store');
        Route::delete('user/image', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\UserImageController::class, 'destroy'])->name('user.image.destroy');
        Route::patch('user/self', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\AuthenticatedUserController::class, 'update']);
        Route::post('user/send-email-verification', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\EmailVerificationController::class, 'store']);
    }

    public function forUsers()
    {
        Route::resource('users', \Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\UserController::class);
    }

    public function forNotifications()
    {
        Route::get('/user/notifications', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\NotificationController::class, 'index']);
        Route::patch('/user/notifications/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\NotificationController::class, 'update']);
    }

    public function forVideos() : void
    {
        Route::get('videos', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideoController::class, 'index']);
        Route::get('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideoController::class, 'show']);
        Route::patch('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideoController::class, 'update']);
        Route::delete('videos/{id}', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\VideoController::class, 'destroy']);
    }

    public function forImageSrcSet() : void
    {
        Route::get('images/{id}/src-set', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\ImageSourceSetController::class, 'show']);
    }

    public function forPagePaths() : void
    {
        Route::get('pages/{id}/paths', [\Ikoncept\Fabriq\Http\Controllers\Api\Fabriq\PagePathController::class, 'index'])
            ->name('pages.paths.index')
            ->middleware('locale');
    }

}
