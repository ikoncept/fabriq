<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Middleware\LocaleMiddleware;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Infab\Core\Traits\ApiControllerTrait;

class PagePathController
{
    use ApiControllerTrait;

    protected $middleware = [LocaleMiddleware::class];


    public function index(Request $request, int $id) : JsonResponse
    {
        $page = Fabriq::getModelClass('page')->whereId($id)
            ->with('slugs', 'menuItems', 'latestSlug')
            ->firstOrFail();



        // $paths = $page->paths->map(function($item) use ($page) {
        //     if(!isset($item[App::currentLocale()])) {
        //         return null;
        //     }
        //     return [
        //         // 'relative_path' => $item[App::currentLocale()]['0'] ?? $page->latestSlug->slug . 'hej',
        //         'absolute_path' => $page->getAbsolutePath($item[App::currentLocale()]['0'], $page->latestSlug->slug),
        //         // 'absolute_localized_path' => config('fabriq.front_end_domain'). '/' . App::currentLocale() . ($item[App::currentLocale()]['0'] ?? $page->latestSlug->slug),
        //         'perma_link' => $page->getPermalinkPath($page->id)
        //     ];
        // })->filter()->first();
        $paths = $page->transformPaths();


        return $this->respondWithArray(['data' => $paths]);
    }
}
