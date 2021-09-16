<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Transformers\ConfigTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class ConfigController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Return config
     *
     * @return JsonResponse
     */
    public function index() : JsonResponse
    {
        $fabriqConfig = config('fabriq');
        $supportedLocales = config('translatable-revisions.supportedLocales');
        $config = array_merge($fabriqConfig, ['supported_locales' => $supportedLocales]);

        return $this->respondWithItem($config, new ConfigTransformer);
    }

}
