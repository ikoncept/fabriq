<?php

namespace Ikoncept\Fabriq\Models;

use Spatie\Tags\Tag as SpatieTag;

class Tag extends SpatieTag
{
    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saving(function ($tag) {
            $tag->plain_name = $tag->name;
        });
    }
}
