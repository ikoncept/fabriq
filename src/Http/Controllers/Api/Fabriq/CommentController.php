<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\DeleteCommentRequest;
use Ikoncept\Fabriq\Http\Requests\UpdateCommentRequest;
use Ikoncept\Fabriq\Models\Comment;
use Illuminate\Http\JsonResponse;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;

class CommentController extends ApiController
{
    use ApiControllerTrait;

    public function update(UpdateCommentRequest $request, int $id): JsonResponse
    {
        $comment = Comment::findOrFail($id);
        $comment->comment = $request->comment;
        $comment->edited_at = now();
        $comment->save();

        return $this->respondWithItem($comment, Fabriq::getTransformerFor('comment'));
    }

    public function destroy(DeleteCommentRequest $request, int $id): JsonResponse
    {
        $comment = Comment::whereId($id)
            ->with('notifications')
            ->firstOrFail();

        $comment->notifications->each(function ($item) {
            $item->delete();
        });

        $comment->delete();

        return $this->respondWithSuccess('Comment was deleted');
    }
}
