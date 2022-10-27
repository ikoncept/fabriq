<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\ContentGetters\ButtonGetter;
use Ikoncept\Fabriq\ContentGetters\ButtonsGetter;
use Ikoncept\Fabriq\ContentGetters\FileGetter;
use Ikoncept\Fabriq\ContentGetters\ImageGetter;
use Ikoncept\Fabriq\ContentGetters\VideoGetter;
use Ikoncept\Fabriq\Database\Factories\SmartBlockFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Infab\TranslatableRevisions\Models\RevisionMeta;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;

class SmartBlock extends Model
{
    use HasFactory, HasTranslatedRevisions;

    public const RELATIONSHIPS = [];

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'smart_block';

    protected static function newFactory(): SmartBlockFactory
    {
        return SmartBlockFactory::new();
    }

    /**
     * Get the options for the revisions.
     */
    public function getRevisionOptions(): RevisionOptions
    {
        return RevisionOptions::create()
            ->registerSpecialTypes(['image', 'video', 'file', 'buttons', 'button'])
            ->registerGetters([
                'image' => 'getImages',
                'repeater' => 'getRepeater',
                'button' => 'getButton',
                'buttons' => 'getButtons',
                'file' => 'getFiles',
                'video' => 'getVideos',
            ])
            ->registerDefaultTemplate('smart_block')
            ->registerCacheTagsToFlush(['cms_pages', 'cms_smart_blocks']);
    }

    /**
     * Set localized content.
     *
     * @param  array  $value
     * @return void
     */
    public function setLocalizedContentAttribute($value)
    {
        foreach ($value as $key => $localeContent) {
            $this->updateContent($localeContent, (string) $key);
        }
    }

    /**
     * @param  RevisionMeta  $meta
     * @return mixed
     */
    public function getImages(RevisionMeta $meta)
    {
        return ImageGetter::get($meta, $this->isPublishing);
    }

    /**
     * @param  RevisionMeta  $meta
     * @return mixed
     */
    public function getFiles(RevisionMeta $meta)
    {
        return FileGetter::get($meta, $this->isPublishing);
    }

    /**
     * @param  RevisionMeta  $meta
     * @return mixed
     */
    public function getVideos(RevisionMeta $meta)
    {
        return VideoGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for button.
     *
     * @param  RevisionMeta  $meta
     * @return mixed
     */
    public function getButton(RevisionMeta $meta)
    {
        return ButtonGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for buttons.
     *
     * @param  RevisionMeta  $meta
     * @return mixed
     */
    public function getButtons(RevisionMeta $meta)
    {
        return ButtonsGetter::get($meta, $this->isPublishing);
    }

    /**
     * Search for smart blocks.
     *
     * @param  Builder  $query
     * @param  string  $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereLike(['name'], $search);
    }
}
