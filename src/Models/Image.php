<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\ImageFactory;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

class Image extends Model implements HasMedia
{
    use HasFactory, HasTags, InteractsWithMedia;

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'image';

    protected $fillable = ['alt_text', 'caption', 'custom_crop', 'x_position', 'y_position'];

    /**
     * Create a new factory.
     */
    protected static function newFactory(): ImageFactory
    {
        return ImageFactory::new();
    }

    public static function getTagClassName(): string
    {
        return Fabriq::getFqnModel('tag');
    }

    protected $with = ['media'];

    public const RELATIONSHIPS = ['tags'];

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->nonQueued()
            ->fit(Fit::Crop, 480, 320)
            ->format(config('fabriq.enable_webp') ? 'webp' : 'jpg')
            ->quality(80);

        $this->addMediaConversion('og_image')
            ->fit(Fit::Max, 1200)
            ->format('jpg')
            ->quality(80);

        if (config('fabriq.enable_webp')) {
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
     * Search for an image.
     *
     * @param  string|null  $search
     */
    public function scopeSearch(Builder $query, $search): Builder
    {
        $searchColumns = ['media.file_name', 'media.name', 'alt_text', 'tags.plain_name'];

        return $query->whereLike($searchColumns, $search);
    }

    /**
     * Set tags.
     *
     * @param  array  $value
     * @return void
     */
    public function setImageTagsAttribute($value)
    {
        if ($value) {
            $this->syncTagsWithType($value, 'images');
        } else {
            $this->syncTagsWithType([], 'images');
        }
    }

    public function saveMedia(bool $fromUrl = false, string $collection = 'images', string $url = '', ?string $name = null): void
    {
        if ($fromUrl) {
            [$width, $height] = getimagesize(request()->input('url', $url));
            $this->addMediaFromUrl(request()->input('url', $url))
                ->withResponsiveImages()
                ->withCustomProperties(['width' => $width, 'height' => $height, 'processing' => true, 'processing_failed' => false])
                ->setName($name ?? basename($url))
                ->toMediaCollection($collection);

            return;
        }
        [$width, $height] = getimagesize(request()->file('image'));
        $this->addMediaFromRequest('image')
            ->withResponsiveImages()
            ->withCustomProperties([
                'width' => $width,
                'height' => $height,
                'processing' => ($width) ? true : false, 'processing_failed' => false,
            ])
            ->toMediaCollection($collection);
    }
}
