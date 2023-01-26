<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreateBlockTypeRequest;
use Ikoncept\Fabriq\Http\Requests\UpdateBlockTypeRequest;
use Ikoncept\Fabriq\Models\BlockType;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\QueryBuilder;

class BlockTypeController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $request): JsonResponse
    {
        $blockTypes = QueryBuilder::for(BlockType::where('active', 1))
            ->allowedSorts('name', 'id')
            ->defaultSort('name')
            ->get();

        return $this->respondWithCollection($blockTypes, Fabriq::getTransformerFor('blockType'));
    }

    public function store(CreateBlockTypeRequest $request): JsonResponse
    {
        $blockType = new BlockType();
        $blockType->fill($request->validated());
        $blockType->active = true;
        $blockType->type = 'block';
        $blockType->options = [
            'recommended_for' => [],
            'visible_for' => [],
            'hidden_for' => [],
        ];

        $blockType->save();

        return $this->respondWithItem($blockType, Fabriq::getTransformerFor('blockType'), 201);
    }

    public function update(UpdateBlockTypeRequest $request, int $id): JsonResponse
    {
        $blockType = BlockType::findOrFail($id);
        $blockType->fill($request->validated());
        $blockType->save();

        return $this->respondWithItem($blockType, Fabriq::getTransformerFor('blockType'));
    }

    public function destroy(int $id): JsonResponse
    {
        $blockType = BlockType::findOrFail($id);
        $blockType->delete();

        return $this->respondWithSuccess('Block type has been deleted');
    }
}
