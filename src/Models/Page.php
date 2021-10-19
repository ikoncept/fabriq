<?php

namespace Ikoncept\Fabriq\Models;

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
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Infab\TranslatableRevisions\Models\I18nLocale;
use Infab\TranslatableRevisions\Models\RevisionMeta;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;
use Kalnoy\Nestedset\NodeTrait;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Page extends Model implements HasMedia
{
    use HasFactory, HasTranslatedRevisions, InteractsWithMedia, NodeTrait, Commentable;

    const RELATIONSHIPS = ['template', 'template.fields'];

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'page';

    protected $dates = ['published_at'];

    protected $fillable = ['id', 'sortindex'];

    public array $templateSluggable = ['page_title'];

    protected $casts = [
        '_lft' => 'int',
        '_rgt' => 'int',
        'parent_id' => 'int',
    ];

    /**
     * Create a new factory
     */
    protected static function newFactory() : PageFactory
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
            MenuItem::where('page_id', $page->id)->get()->each(function($item) {
                $item->delete();
            });
            DB::table('slugs')->where('model_id', $page->id)
                ->where('model_type', 'Ikoncept\Fabriq\Models\Page')
                ->delete();
        });
    }

    /**
     * Get the options for the revisions.
     */
    public function getRevisionOptions() : RevisionOptions
    {
        return RevisionOptions::create()
            ->registerSpecialTypes(['image', 'video', 'file', 'smartBlock', 'button', 'buttons'])
            ->registerGetters([
                'image' => 'getImages',
                'main_image' => 'getImages',
                'meta_og_image' => 'getImages',
                'logo_image' => 'getImages',
                'repeater' => 'getRepeater',
                'buttons' => 'getButtons',
                'file' => 'getFiles',
                'video' => 'getVideos',
                'button' => 'getButton',
                'buttons' => 'getButtons',
                'smartBlock' => 'getSmartBlock'
            ]);
    }

    /**
     * Getter for images
     *
     * @param RevisionMeta $meta
     * @return mixed
     */
    public function getImages(RevisionMeta $meta)
    {
        return ImageGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for files
     *
     * @param RevisionMeta $meta
     * @return mixed
     */
    public function getFiles(RevisionMeta $meta)
    {
        return FileGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for videos
     *
     * @param RevisionMeta $meta
     * @return mixed
     */
    public function getVideos(RevisionMeta $meta)
    {
        return VideoGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for button
     *
     * @param RevisionMeta $meta
     * @return mixed
     */
    public function getButton(RevisionMeta $meta)
    {
        return ButtonGetter::get($meta, $this->isPublishing);
    }

    /**
     * Getter for buttons
     *
     * @param RevisionMeta $meta
     * @return mixed
     */
    public function getButtons(RevisionMeta $meta)
    {
        return ButtonsGetter::get($meta, $this->isPublishing);
    }


    /**
     * Getter for buttons
     *
     * @param RevisionMeta $meta
     * @return mixed
     */
    public function getSmartBlock(RevisionMeta $meta)
    {
        return SmartBlockGetter::get($meta, $this->isPublishing);
    }

    public function menuItems() : HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('menuItem'));
    }

    public function getPathsAttribute() : Collection
    {
        $slugGroups = collect([]);

        $supportedLocales = Fabriq::getModelClass('locale')->cachedLocales();

        foreach($supportedLocales as $locale => $item) {
            $localizedSlugs = $this->menuItems->map(function($item) use ($locale) {
                if(! $item->ancestors->count()) {
                    return '';
                }
                return collect($item->ancestors)->reduce(function($carry, $subItem) use ($locale) {
                    if(! $subItem->page) {
                        return;
                    }
                    return  $carry . '/' . $subItem->getSlugString($locale);
                }, '') . '/' . $item->getSlugString($locale);
            })->unique();
            $slugGroups->push([$locale => $localizedSlugs]);
        }
        return $slugGroups;
    }

    public function getLocalizedPathsAttribute() : Collection
    {
        $slugGroups = collect([]);
            $localizedSlugs = $this->menuItems->map(function($item) {
                if(! $item->ancestors->count()) {
                    return '';
                }
                return collect($item->ancestors)->reduce(function($carry, $subItem) {
                    if(! $subItem->page) {
                        return;
                    }
                    return  $carry . '/' . $subItem->getSlugString();
                }, '') . '/' . $item->getSlugString();
            })->unique();
        $slugGroups->push($localizedSlugs);

        return $slugGroups;
    }
    /**
     * Relation for slugs
     *
     * @return MorphMany
     */
    public function slugs() : MorphMany
    {
        return $this->morphMany(Fabriq::getFqnModel('slug'), 'model');
    }


    /**
     * Scope query to find by Slug.
     *
     * @param Builder $query
     * @param string $slug
     * @return Builder
     */
    public function scopeWhereSlug(Builder $query, string $slug) : Builder
    {
        return $query->whereHas('slugs', function(Builder $query) use ($slug) {
            $query->where('slug', $slug);
        });
    }

    /**
     * Search for pages
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search) : Builder
    {
        return $query->whereLike(['name','template.name'], $search);
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

}
