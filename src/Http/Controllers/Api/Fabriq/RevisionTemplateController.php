<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Transformers\TemplateTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Infab\TranslatableRevisions\Models\RevisionTemplate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class RevisionTemplateController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $request): JsonResponse
    {
        $templates = QueryBuilder::for(RevisionTemplate::class)
            ->allowedFilters(
                AllowedFilter::exact('type')
            )->get();

        return $this->respondWithCollection($templates, new TemplateTransformer);
    }
}
