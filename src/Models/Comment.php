<?php

namespace Ikoncept\Fabriq\Models;

use DOMDocument;
use DOMXPath;
use Ikoncept\Fabriq\Database\Factories\CommentFactory;
use Ikoncept\Fabriq\Events\CommentPosted;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Ikoncept\Fabriq\Models\Notification;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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

    protected $dates = [
        'anonymized_at'
    ];

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
            $doc = new DOMDocument();
            $doc->loadHTML($model->comment);
            $xpath = new DOMXPath($doc);
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

        static::deleting(function ($model) {
            if($model->children->count()) {
                $model->comment = 'Borttagen kommentar';
                $model->anonymized_at = now();
                $model->save();

                return false;
            }
        });

        // If the last child comment is deleted on a deleted
        // parent, the parent should be deleted
        static::deleted(function ($model) {
            if(! $model->parent_id) {
                return;
            }
            if(! $model->parent->children->count()) {
                $model->parent->delete();
            }
        });
    }


    protected $fillable = [
        'user_id',
        'comment',
        'parent_id',
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

    public function children() : HasMany
    {
        return $this->hasMany(self::class, 'parent_id', 'id')
            ->orderBy('created_at');
    }

    public function parent() : BelongsTo
    {
        return $this->belongsTo(self::class, 'parent_id', 'id');
    }
}
