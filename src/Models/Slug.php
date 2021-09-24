<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

class Slug extends Model
{
    use HasSlug;

    protected $guarded = ['id'];

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'slug';

    /**
     * Get the options for generating the slug.
     */
    public function getSlugOptions() : SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('source_string')
            ->saveSlugsTo('slug');
    }

    public function model(): MorphTo
    {
        return $this->morphTo();
    }

}
