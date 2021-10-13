<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Locale;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Transformers\ConfigTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
        $fabriqConfig = collect(config('fabriq'))->only([
            'models',
            'modules',
            'front_end_domain'
        ])->toArray();

        $supportedLocales = Fabriq::getModelClass('locale')->cachedLocales();
        $config = array_merge($fabriqConfig, ['supported_locales' => $supportedLocales]);

        return $this->respondWithItem($config, new ConfigTransformer);
    }

}
