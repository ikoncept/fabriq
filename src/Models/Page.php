<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Concerns\BroadcastsModelEvents;
use Ikoncept\Fabriq\Concerns\HasPaths;
use Ikoncept\Fabriq\ContentGetters\ButtonGetter;
use Ikoncept\Fabriq\ContentGetters\ButtonsGetter;
use Ikoncept\Fabriq\ContentGetters\FileGetter;
use Ikoncept\Fabriq\ContentGetters\ImageGetter;
use Ikoncept\Fabriq\ContentGetters\SmartBlockGetter;
use Ikoncept\Fabriq\ContentGetters\VideoGetter;
use Ikoncept\Fabriq\Database\Factories\PageFactory;
use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Traits\Commentable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\DB;
use Infab\TranslatableRevisions\Models\RevisionMeta;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use BroadcastsModelEvents, Commentable, HasFactory, HasPaths, HasTranslatedRevisions, InteractsWithMedia, NodeTrait;

    public const RELATIONSHIPS = ['template', 'template.fields'];

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'page';

    /**
     * @var array
     */
    protected $dates = ['published_at'];

    protected $fillable = ['id', 'sortindex'];

    public array $templateSluggable = ['page_title'];

    protected $casts = [
        '_lft' => 'int',
        '_rgt' => 'int',
        'parent_id' => 'int',
        'published_at' => 'datetime',
    ];

    /**
     * Create a new factory.
     */
    protected static function newFactory(): PageFactory
    {
        return PageFactory::new();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($page) {
            MenuItem::where('page_id', $page->id)->get()->each(function ($item) {
                $item->delete();
            });

            DB::table('slugs')->where('model_id', $page->id)
                ->where('model_type', config('fabriq.morph_map.'.Fabriq::getFqnModel('page')) ?? Fabriq::getFqnModel('page'))
                ->delete();
        });
        static::created(function ($page) {
            $content = [
                'page_title' => $page->name,
            ];
            $localPage = Fabriq::getModelClass('page')
                ->select('id', 'revision')
                ->find($page->id);
            $supportedLocales = Fabriq::getModelClass('locale')->cachedLocales();
            $supportedLocales->each(function ($locale, $key) use ($content, $localPage) {
                $localPage->updateContent($content, $key, $localPage->revision);
            });
        });
    }

    /**
     * Get the options for the revisions.
     */
    public function getRevisionOptions(): RevisionOptions
    {
        return RevisionOptions::create()
            ->registerSpecialTypes(['image', 'video', 'file', 'smartBlock', 'button', 'buttons'])
            ->registerGetters([
                'image' => 'getImages',
                'main_image' => 'getImages',
                'meta_og_image' => 'getImages',
                'logo_image' => 'getImages',
                'repeater' => 'getRepeater',
                'file' => 'getFiles',
                'video' => 'getVideos',
                'button' => 'getButton',
                'buttons' => 'getButtons',
                'smartBlock' => 'getSmartBlock',
            ])
            ->registerCacheTagsToFlush(['fabriq_menu', 'fabriq_pages|slug'])
            ->setIndexable(indexable: true, indexableKeys: ['page_title', 'header'], titleKey: 'page_title');
    }

    /**
     * Getter for images.
     *
     * @return mixed
     */
    public function getImages(RevisionMeta $meta)
    {
        return ImageGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for files.
     *
     * @return mixed
     */
    public function getFiles(RevisionMeta $meta)
    {
        return FileGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for videos.
     *
     * @return mixed
     */
    public function getVideos(RevisionMeta $meta)
    {
        return VideoGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for button.
     *
     * @return mixed
     */
    public function getButton(RevisionMeta $meta)
    {
        return ButtonGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for buttons.
     *
     * @return mixed
     */
    public function getButtons(RevisionMeta $meta)
    {
        return ButtonsGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for buttons.
     *
     * @return mixed
     */
    public function getSmartBlock(RevisionMeta $meta)
    {
        return SmartBlockGetter::get($meta, $this->isPublishing);
    }

    public function menuItems(): HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('menuItem'));
    }

    public function updatedByUser(): BelongsTo
    {
        return $this->belongsTo(Fabriq::getFqnModel('user'), 'updated_by', 'id');
    }

    /**
     * Relation for slugs.
     */
    public function slugs(): MorphMany
    {
        return $this->morphMany(Fabriq::getFqnModel('slug'), 'model');
    }

    public function latestSlug(): MorphOne
    {
        return $this->morphOne(Fabriq::getFqnModel('slug'), 'model')->ofMany([], function ($query) {
            $query->where('locale', app()->getLocale());
        });
    }

    /**
     * Scope query to find by Slug.
     */
    public function scopeWhereSlug(Builder $query, string $slug): Builder
    {
        return $query->whereHas('slugs', function (Builder $query) use ($slug) {
            $query->where('slug', $slug);
        });
    }

    /**
     * Search for pages.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereLike(['name', 'template.name'], $search);
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
}
