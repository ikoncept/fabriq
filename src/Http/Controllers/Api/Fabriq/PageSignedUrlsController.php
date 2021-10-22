<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\Page;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Infab\Core\Traits\ApiControllerTrait;

class PageSignedUrlsController extends ApiController
{
    use ApiControllerTrait;

    public function show(Request $request, int $id) : JsonResponse
    {
        $page = Fabriq::getModelClass('page')->findOrFail($id);

        $signedURL = URL::signedRoute('pages.show.preview', ['slug' => $page->slugs->first()->slug]);

        return $this->respondWithArray([
            'computed_path' => $page->localizedPaths,
            'signed_url' => $signedURL,
            'encoded_signed_url' => base64_encode($signedURL)
        ]);
    }
}
