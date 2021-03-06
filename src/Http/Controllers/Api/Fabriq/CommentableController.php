<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Http\Requests\CreateCommentRequest;
use Ikoncept\Fabriq\Models\Comment;
use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Transformers\CommentTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;

class CommentableController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Model map
     *
     * @var array
     */
    protected $modelMap = [
        'pages' => 'page'
    ];

    public function index(Request $request, string $modelName, int $modelId) : JsonResponse
    {
        if(! array_key_exists($modelName, $this->modelMap)) {
            return $this->errorWrongArgs('The specified model is not commentable');
        }

        $modelClass = $this->modelMap[$modelName];
        $model = Fabriq::getModelClass($modelClass)->where('id', $modelId)
            ->with('comments', 'comments.user', 'comments.user.roles', 'comments.children', 'comments.children.user')
            ->firstOrFail();

        return $this->respondWithCollection($model->comments, new CommentTransformer);
    }

    public function store(CreateCommentRequest $request, string $modelName, int $modelId) : JsonResponse
    {
        if(! array_key_exists($modelName, $this->modelMap)) {
            return $this->errorWrongArgs('The specified model is not commentable');
        }

        $modelClass = $this->modelMap[$modelName];
        $model = Fabriq::getModelClass($modelClass)->where('id', $modelId)
            ->firstOrFail();
        $comment = $model->commentAs($request->user(), $request->comment, $request->parent_id ?? null);

        return $this->respondWithItem($comment, new CommentTransformer, 201);
    }
}
