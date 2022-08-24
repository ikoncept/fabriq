<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreateSmartBlockRequest;
use Ikoncept\Fabriq\Models\SmartBlock;
use Ikoncept\Fabriq\Transformers\SmartBlockTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class SmartBlockController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Get index of the resource.
     * @param  Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(SmartBlock::RELATIONSHIPS);
        $paginator = QueryBuilder::for(Fabriq::getFqnModel('smartBlock'))
            ->allowedSorts('name', 'updated_at')
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($paginator, new SmartBlockTransformer);
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(SmartBlock::RELATIONSHIPS);
        $smartBlock = SmartBlock::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($smartBlock, new SmartBlockTransformer);
    }

    public function store(CreateSmartBlockRequest $request): JsonResponse
    {
        $smartBlock = new SmartBlock();
        $smartBlock->name = $request->name;
        $smartBlock->save();

        return $this->respondWithItem($smartBlock, new SmartBlockTransformer, 201);
    }

    public function update(Request $request, int $id): JsonResponse
    {
        $smartBlock = SmartBlock::findOrFail($id);
        $smartBlock->name = $request->name;
        $smartBlock->localizedContent = $request->localizedContent;
        $smartBlock->touch();
        $smartBlock->save();

        return $this->respondWithItem($smartBlock, new SmartBlockTransformer);
    }

    public function destroy(int $id): JsonResponse
    {
        $smartBlock = SmartBlock::findOrFail($id);

        $smartBlock->delete();

        return $this->respondWithSuccess('Smart block has been deleted successfully');
    }
}
