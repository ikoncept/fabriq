<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\VideoFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Image\Manipulations;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;
use Illuminate\Support\Str;

class Video extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, HasTags;


    const RELATIONSHIPS = ['tags'];

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'video';

    protected static function newFactory() : VideoFactory
    {
        return VideoFactory::new();
    }

    protected $with = ['media'];

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('thumb')
              ->nonQueued()
              ->extractVideoFrameAtSecond(20)
              ->performOnCollections('videos')
              ->crop(Manipulations::CROP_CENTER, 480, 320);

        $this->addMediaConversion('poster')
              ->performOnCollections('videos')
              ->extractVideoFrameAtSecond(0);
    }

    /**
     * Set tags
     *
     * @param array $value
     * @return void
     */
    public function setVideoTagsAttribute($value)
    {
        if($value) {
            $this->syncTagsWithType($value, 'videos');
        } else {
            $this->syncTagsWithType([], 'videos');
        }
    }

    /**
     * Search for a video
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
}
