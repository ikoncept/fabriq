<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Str;

class Invitation extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected static function booted()
    {
        static::creating(function ($model) {
            $model->uuid = Str::uuid();
            self::where('user_id', $model->user_id)->get()->each(function($item) {
                $item->delete();
            });
        });
    }

    public function user() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function invitedBy() : HasOne
    {
        return $this->hasOne(User::class, 'id', 'invited_by');
    }
}
