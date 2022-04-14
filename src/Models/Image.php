<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\ImageFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
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

    protected $fillable = ['alt_text', 'caption', 'custom_crop', 'x_position', 'y_position'];

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
              ->width(360)
              ->crop(Manipulations::CROP_CENTER, 480, 320)
              ->format(config('fabriq.enable_webp') ? 'webp' : 'jpg')
              ->quality(80);

        if(config('fabriq.enable_webp')) {
            $this->addMediaConversion('webp')
                ->format('webp')
                ->quality(85);
        }
    }

    public function mediaImages(): MorphMany
    {
        return $this->morphMany(config('media-library.media_model'), 'model')
            ->where('collection_name', 'images');
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

    public function saveMedia(bool $fromUrl = false, string $collection = 'images' , string $url = '') : void
    {
        if($fromUrl) {
            list($width, $height) = getimagesize(request()->input('url', $url));
            $this->addMediaFromUrl(request()->input('url', $url))
                ->withResponsiveImages()
                ->withCustomProperties(['width' => $width, 'height' => $height])
                ->toMediaCollection($collection);
            return;
        }
        list($width, $height) = getimagesize(request()->file('image'));
        $this->addMediaFromRequest('image')
            ->withResponsiveImages()
            ->withCustomProperties(['width' => $width, 'height' => $height])
            ->toMediaCollection($collection);
    }
}
