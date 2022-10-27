<?php

namespace Ikoncept\Fabriq\Http\Controllers;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\App;
use Infab\Core\Traits\ApiControllerTrait;

class PermalinksRedirectController extends Controller
{
    use ApiControllerTrait;

    /**
     * Return redirect or paths.
     *
     * @param  Request  $request
     * @param  string  $hash
     * @param  string  $locale
     * @return JsonResponse|RedirectResponse
     */
    public function __invoke(Request $request, string $hash, string $locale = 'sv')
    {
        App::setLocale($locale);
        $page = Fabriq::getModelClass('page')->whereHash($hash)
            ->with('slugs', 'menuItems', 'latestSlug')
            ->firstOrFail();

        $paths = $page->transformPaths();

        if (request()->wantsJson()) {
            return $this->respondWithArray([
                'data' => $paths,
            ]);
        }

        return Response()->redirectTo($paths['absolute_path']);
    }
}
