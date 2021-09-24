<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\ContentGetters\ImageGetter;
use Ikoncept\Fabriq\Database\Factories\ContactFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;
use Spatie\Tags\HasTags;

class Contact extends Model
{
    use HasFactory, HasTranslatedRevisions, HasTags;

    const RELATIONSHIPS = ['images', 'tags'];

    protected $guarded = ['content', 'localizedContent'];

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'contact';

    /**
     * Create a new factory
     */
    protected static function newFactory() : ContactFactory
    {
        return ContactFactory::new();
    }


    /**
     * Get the options for the revisions.
     */
    public function getRevisionOptions() : RevisionOptions
    {
        return RevisionOptions::create()
            ->registerDefaultTemplate('contact')
            ->registerSpecialTypes(['image'])
            ->registerGetters([
                'image' => 'getImages',
            ]);
    }

    /**
     * Getter for images
     *
     * @param mixed $meta
     * @return mixed
     */
    public function getImages($meta)
    {
        return ImageGetter::get($meta, $this->isPublishing);
    }

    /**
     * Get a collection of all images the model has.
     *
     * @return \Illuminate\Database\Eloquent\Relations\MorphToMany
     */
    public function images() : MorphToMany
    {
        return $this->morphToMany(\Ikoncept\Fabriq\Models\Image::class, 'imageable')
            ->withPivot('id', 'sortindex')
            ->orderBy('imageables.sortindex');
    }

    /**
     * Search for contacts
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search) : Builder
    {
        return $query->whereLike(['name', 'email', 'phone'], $search)
            ->orWhereHas('tags', function($query) use ($search) {
                return $query->where('name->sv', 'like', '%' . $search . '%');
            });
    }

    /**
     * Set tags
     *
     * @param array $value
     * @return void
     */
    public function setContactTagsAttribute($value)
    {
        if($value) {
            $this->syncTagsWithType($value, 'contacts');
        } else {
            $this->syncTagsWithType([], 'contacts');
        }
    }

    /**
     * Set localized content
     *
     * @param array $value
     * @return void
     */
    public function setLocalizedContentAttribute($value)
    {
        foreach($value as $key => $localeContent) {
            $this->updateContent($localeContent, (string) $key);
        }
    }

    /**
     * Get the title for the event
     *
     * @return string|null
     */
    public function getImageAttribute()
    {
        $meta = $this->meta()->where('meta_key', 'image')->first();
        if(! $meta) {
           return null;
        }
        return $this->getImages($meta);
    }


}
