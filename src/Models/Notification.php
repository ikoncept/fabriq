<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\NotificationFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Notification extends Model
{
    use HasFactory;

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'notification';

    protected static function newFactory() : NotificationFactory
    {
        return NotificationFactory::new();
    }

    const RELATIONSHIPS = ['notifiable', 'notifiable.user', 'notifiable.commentable'];

    protected $fillable = ['user_id', 'content'];

    protected $dates = ['cleared_at', 'notified_at'];

    public function notifiable() : MorphTo
    {
        return $this->morphTo();
    }

    public function scopeUnseen(Builder $query) : Builder
    {
        return $query->whereNull('cleared_at');
    }

    public function scopeSeen(Builder $query) : Builder
    {
        return $query->whereNotNull('cleared_at');
    }
}
