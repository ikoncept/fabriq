<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Models\BlockType;
use Ikoncept\Fabriq\Transformers\BlockTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\QueryBuilder;

class BlockTypeController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $requst) : JsonResponse
    {
        $blockTypes = QueryBuilder::for(BlockType::where('active', 1))
            ->allowedSorts('name', 'id')
            ->defaultSort('name')
            ->get();

        return $this->respondWithCollection($blockTypes, new BlockTypeTransformer());
    }
}
