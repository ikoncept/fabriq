<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\ContentGetters\FileGetter;
use Ikoncept\Fabriq\Database\Factories\MenuItemFactory;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Infab\TranslatableRevisions\Models\RevisionMeta;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;
use Kalnoy\Nestedset\NodeTrait;

class MenuItem extends Model
{
    use HasFactory, HasTranslatedRevisions, NodeTrait;

    public const RELATIONSHIPS = ['page'];

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'menu_item';

    protected $with = ['page'];

    protected $appends = ['title'];

    protected $guarded = [];

    protected $touches = ['menu'];

    public array $templateSluggable = [];

    protected $casts = [
        'menu_id' => 'int',
        '_lft' => 'int',
        '_rgt' => 'int',
    ];

    protected static function newFactory(): MenuItemFactory
    {
        return MenuItemFactory::new();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::deleting(function ($menuItem) {
            DB::table('slugs')->where('model_id', $menuItem->id)
                ->where('model_type', 'Ikoncept\Fabriq\Models\MenuItem')
                ->delete();
        });
    }

    /**
     * Get the options for the revisions.
     */
    public function getRevisionOptions(): RevisionOptions
    {
        return RevisionOptions::create()
            ->registerDefaultTemplate('menu-item')
            ->registerSpecialTypes(['file'])
            ->registerGetters([
                'file' => 'getFiles',
            ])
            ->registerCacheKeysToFlush(['fabriq_menu']);
    }

    public function page(): BelongsTo
    {
        return $this->belongsTo(Fabriq::getFqnModel('page'));
    }

    /**
     * Getter for files.
     *
     * @return mixed
     */
    public function getFiles(RevisionMeta $meta)
    {
        return FileGetter::get($meta);
    }

    /**
     * Get the title attribute.
     */
    public function getTitleAttribute(): string
    {
        if ($this->type === 'internal') {
            return $this->getSlug()->source_string ?? '';
        }

        $content = $this->getFieldContent(null, 'sv');

        return $content['title'] ?? '';
    }

    /**
     * Get string of the slug.
     *
     * @return string
     */
    public function getSlugString(string $locale = '')
    {
        return $this->getSlug($locale)->slug ?? '';
    }

    /**
     * Get the slug.
     *
     * @return mixed
     */
    public function getSlug(string $locale = '')
    {
        if (! $locale) {
            $locale = app()->getLocale();
        }
        try {
            return $this->page->slugs->where('locale', $locale)->first();
        } catch (\Throwable $th) {
            return new Slug;
        }
    }

    public function getRelativePathAttribute(): string
    {
        if ($this->ancestors->count()) {
            $collection = new Collection($this->ancestors);

            return $collection->reduce(function ($carry, $subItem) {
                /** @var MenuItem $subItem * */
                if (! $subItem->page) {
                    return;
                }

                return $carry.'/'.$subItem->getSlugString();
            }, '').'/'.$this->getSlugString();
        }

        return $this->getSlugString();
    }

    /**
     * Skip setting title.
     *
     * @param  mixed  $value
     */
    public function setTitleAttribute($value): void {}

    /**
     * Skip setting page attribute.
     *
     * @param  mixed  $value
     */
    public function setPageAttribute($value): void {}

    public function menu(): BelongsTo
    {
        return $this->belongsTo(Fabriq::getFqnModel('menu'));
    }
}
