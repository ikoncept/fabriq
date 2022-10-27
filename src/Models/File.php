<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\FileFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Tags\HasTags;

class File extends Model implements HasMedia
{
    use HasFactory, HasTags, InteractsWithMedia;

    public const RELATIONSHIPS = ['tags'];

    protected $with = ['media'];

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'file';

    /**
     * Create a new factory.
     */
    protected static function newFactory(): FileFactory
    {
        return FileFactory::new();
    }

    public function registerMediaConversions(Media $media = null): void
    {
        $this->addMediaConversion('file_thumb')
            ->performOnCollections('files')
            ->nonQueued()
            ->width(480);
    }

    /**
     * Set tags.
     *
     * @param  array  $value
     * @return void
     */
    public function setFileTagsAttribute($value)
    {
        if ($value) {
            $this->syncTagsWithType($value, 'files');
        } else {
            $this->syncTagsWithType([], 'files');
        }
    }

    /**
     * Search for an image.
     *
     * @param  Builder  $query
     * @param  string|null  $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, $search): Builder
    {
        $searchColumns = ['media.file_name', 'media.name', 'readable_name'];

        return $query->whereLike($searchColumns, $search)
            ->orWhereHas('tags', function ($query) use ($search) {
                return $query->where('name->sv', 'like', '%'.$search.'%')
                    ->orWhere('name->en', 'like', '%'.$search.'%');
            });
    }
}
