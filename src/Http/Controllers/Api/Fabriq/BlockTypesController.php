<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Models\BlockType;
use Ikoncept\Fabriq\Transformers\BlockTypeTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class BlockTypesController extends ApiController
{

    use ApiControllerTrait;

    public function index(Request $requst) : JsonResponse
    {
        $blockTypes = BlockType::where('active', 1)->get();

        return $this->respondWithCollection($blockTypes, new BlockTypeTransformer());
    }
}
