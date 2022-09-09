<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Transformers\ConfigTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Collection;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class ConfigController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Return config.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $config = new Collection(config('fabriq'));
        $fabriqConfig = $config->only([
            'models',
            'modules',
            'front_end_domain',
            'extras'
        ])->toArray();

        $supportedLocales = Fabriq::getModelClass('locale')->cachedLocales();
        $config = array_merge($fabriqConfig, ['supported_locales' => $supportedLocales]);

        return $this->respondWithItem($config, new ConfigTransformer);
    }
}
