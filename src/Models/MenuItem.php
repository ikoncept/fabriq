<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\MenuItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Infab\TranslatableRevisions\Traits\HasTranslatedRevisions;
use Infab\TranslatableRevisions\Traits\RevisionOptions;
use Kalnoy\Nestedset\NodeTrait;

class MenuItem extends Model
{
    use HasFactory, NodeTrait, HasTranslatedRevisions;

    const RELATIONSHIPS = ['page'];

    protected $with = ['page'];

    protected $appends = ['title'];

    protected $guarded = [];

    public array $templateSluggable = [];

    protected $casts = [
        'menu_id' => 'int',
        '_lft' => 'int',
        '_rgt' => 'int',
    ];

    protected static function newFactory() : MenuItemFactory
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
    public function getRevisionOptions() : RevisionOptions
    {
        return RevisionOptions::create()
            ->registerDefaultTemplate('menu-item');
    }


    public function page() : BelongsTo
    {
        return $this->belongsTo(\Ikoncept\Fabriq\Models\Page::class);
    }

    /**
     * Get the title attribute
     *
     * @return string
     */
    public function getTitleAttribute() : string
    {
        if($this->type === 'external') {
            $content = $this->getFieldContent(null, 'sv');
            return $content['title'] ?? '';
        }

        return $this->getSlug()->source_string ?? '';
    }

    /**
     * Get string of the slug
     *
     * @return string
     */
    public function getSlugString(string $locale = '')
    {
        return $this->getSlug($locale)->slug ?? '';
    }

    /**
     * Get the slug
     *
     * @param string $locale
     * @return mixed
     */
    public function getSlug(string $locale = '')
    {
        if(! $locale) {
            $locale = app()->getLocale();
        }
        try {
            return $this->page->slugs->where('locale', $locale)->first();
        } catch (\Throwable $th) {
            return new Slug();
        }
    }

    public function getRelativePathAttribute() : string
    {
        if($this->ancestors->count()) {
            return collect($this->ancestors)->reduce(function($carry, $subItem) {
                if(! $subItem->page) {
                    return;
                }
                return  $carry .'/'. $subItem->getSlugString();
            }, '') . '/' . $this->getSlugString();
        }

        return $this->getSlugString();
    }

    /**
     * Skip setting title
     *
     * @param mixed $value
     * @return void
     */
    public function setTitleAttribute($value) : void
    {

    }

    /**
     * Skip setting page attribute
     *
     * @param mixed $value
     * @return void
     */
    public function setPageAttribute($value) : void
    {

    }
}
