<?php

namespace Ikoncept\Fabriq\Traits;

use Ikoncept\Fabriq\Models\Comment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;

trait Commentable
{

    public function comments() : MorphMany
    {
        return $this->morphMany(Comment::class, 'commentable');
    }

    public function comment(string $comment): Model
    {
        return $this->newComment(['comment' => $comment]);
    }

    public function commentAs(Model $user, string $comment): Model
    {
        return $this->newComment([
            'comment' => $comment,
            'user_id' => $user->getKey(),
        ]);
    }

    private function newComment(array $data) : Model
    {
        return $this->comments()->create(array_merge(
            $data,
            [
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
            ]
        ));
    }
}
