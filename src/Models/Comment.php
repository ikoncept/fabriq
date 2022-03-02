<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\CommentFactory;
use Ikoncept\Fabriq\Events\CommentPosted;
use Ikoncept\Fabriq\Fabriq;
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
            $filtered = $xpath->query("//span[@data-email]");
            if($filtered) {
                foreach($filtered as $filter) {
                    $email = $filter->getAttribute('data-email');
                    $user = User::whereEmail($email)->first();
                    if($user) {
                        $notification = $model->notifications()->create([
                            'user_id' => $user->id,
                        ]);
                        CommentPosted::dispatch($notification, $model);
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
        return $this->belongsTo(Fabriq::getFqnModel('user'));
    }

    public function notifications() : MorphMany
    {
        return $this->morphMany(Fabriq::getFqnModel('notification'), 'notifiable');
    }
}
