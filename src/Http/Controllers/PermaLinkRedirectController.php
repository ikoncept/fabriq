<?php

namespace Ikoncept\Fabriq\Http\Controllers;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Page;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;

class PermalinkRedirectController extends Controller
{
    public function __invoke(string $hash, string $locale = 'sv')
    {
        App::setLocale($locale);
        $page = Fabriq::getModelClass('page')->whereHash($hash)
            ->with('slugs', 'menuItems', 'latestSlug')
            ->firstOrFail();

        $paths = $page->transformPaths();

        return Response()->redirectTo($paths['absolute_path']);
    }
}
