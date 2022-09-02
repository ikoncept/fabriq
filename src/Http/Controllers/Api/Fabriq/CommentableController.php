<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreateCommentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class CommentableController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Model map.
     *
     * @var array
     */
    protected $modelMap = [
        'pages' => 'page',
    ];

    public function index(Request $request, string $modelName, int $modelId): JsonResponse
    {
        if (! array_key_exists($modelName, $this->modelMap)) {
            return $this->errorWrongArgs('The specified model is not commentable');
        }

        $modelClass = $this->modelMap[$modelName];
        $model = Fabriq::getModelClass($modelClass)->where('id', $modelId)
            ->with('comments', 'comments.user', 'comments.user.roles', 'comments.children', 'comments.children.user')
            ->firstOrFail();

        return $this->respondWithCollection($model->comments, Fabriq::getTransformerFor('comment'));
    }

    public function store(CreateCommentRequest $request, string $modelName, int $modelId): JsonResponse
    {
        if (! array_key_exists($modelName, $this->modelMap)) {
            return $this->errorWrongArgs('The specified model is not commentable');
        }

        $modelClass = $this->modelMap[$modelName];
        $model = Fabriq::getModelClass($modelClass)->where('id', $modelId)
            ->firstOrFail();
        $comment = $model->commentAs($request->user(), $request->comment, $request->parent_id ?? null);

        return $this->respondWithItem($comment, Fabriq::getTransformerFor('comment'), 201);
    }
}
