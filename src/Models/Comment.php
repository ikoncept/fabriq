<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\CommentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ikoncept\Fabriq\Models\Notification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class Comment extends Model
{
    use HasFactory;

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'comment';

    /**
     * Create a new factory
     */
    protected static function newFactory() : CommentFactory
    {
        return CommentFactory::new();
    }

    protected static function booted() : void
    {
        static::saved(function ($model) {
            $doc = new \DOMDocument;
            $doc->loadHTML($model->comment);
            $xpath = new \DOMXpath($doc);
            $filtered = $xpath->query("//span[@data-id]");
            if($filtered) {
                foreach($filtered as $filter) {
                    $name = $filter->getAttribute('data-id');
                    $user = User::where('name', $name)->first();
                    if($user) {
                        $model->notifications()->create([
                            'user_id' => $user->id,
                        ]);
                    }
                }
            }
        });
    }


    protected $fillable = [
        'user_id',
        'comment',
        'ip_address',
        'user_agent',
    ];

    public function commentable() : MorphTo
    {
        return $this->morphTo();
    }

    public function user() : BelongsTo
    {
        return $this->belongsTo(\Ikoncept\Fabriq\Models\User::class);
    }

    public function notifications() : MorphMany
    {
        return $this->morphMany(Notification::class, 'notifiable');
    }
}
