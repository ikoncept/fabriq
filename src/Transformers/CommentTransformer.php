<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class CommentTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'user', 'page'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Model  $comment
     * @return array
     */
    public function transform(Model $comment)
    {
        return [
            'id' => (int) $comment->id,
            'comment' => (string) $comment->comment,
            'created_at' => (string) $comment->created_at->toIsoString(),
            'user_id' => ($comment->user_id) ? (int) $comment->user_id : null
        ];
    }

    /**
     * Include user
     *
     * @param Model $comment
     * @return \League\Fractal\Resource\Item
     */
    public function includeUser(Model $comment)
    {
        return $this->item($comment->user, new UserTransformer);
    }
}
