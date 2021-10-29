<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;
use Illuminate\Support\Str;

class Image extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTags;

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'image';

    protected $fillable = ['alt_text', 'caption', 'custom_crop', 'x_position', 'y_position', 'tags'];

    /**
     * Create a new factory
     */
    protected static function newFactory() : ImageFactory
    {
        return ImageFactory::new();
    }

    protected $with = ['media'];

    const RELATIONSHIPS = ['tags'];

    public function imageable() : MorphTo
    {
        return $this->morphTo();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->nonQueued()
              ->crop(Manipulations::CROP_CENTER, 480, 320)
              ->quality(80);

        if(config('fabriq.enable_webp')) {
            $this->addMediaConversion('webp')
                ->format('webp')
                ->quality(80);
        }
    }

    /**
     * Search for an image
     *
     * @param Builder $query
     * @param string|null $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, $search) : Builder
    {
        $searchColumns = ['media.file_name', 'media.name', 'alt_text'];

        return $query->whereLike($searchColumns, $search)
            ->orWhereHas('tags', function($query) use ($search) {
                return $query->where('name->sv', 'like', '%' . $search . '%')
                    ->orWhere('name->en', 'like', '%' . $search . '%');
            });
    }

    /**
     * Set tags
     *
     * @param array $value
     * @return void
     */
    public function setImageTagsAttribute($value)
    {
        if($value) {
            $this->syncTagsWithType($value, 'images');
        } else {
            $this->syncTagsWithType([], 'images');
        }
    }

}
