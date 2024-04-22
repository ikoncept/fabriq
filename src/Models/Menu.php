<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Actions\BustCacheWithWebhook;
use Ikoncept\Fabriq\Database\Factories\MenuFactory;
use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Services\CacheBuster;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    public const RELATIONSHIPS = [];

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::saved(function ($menu) {
            $tagsToFlush = (new CacheBuster)->getCacheTags($menu, ['fabriq_menu_'.$menu->slug]);
            (new BustCacheWithWebhook)->handle($tagsToFlush);
        });

        static::deleted(function ($menu) {
            $tagsToFlush = (new CacheBuster)->getCacheTags($menu, ['fabriq_menu_'.$menu->slug]);
            (new BustCacheWithWebhook)->handle($tagsToFlush);
        });
    }

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'menu';

    protected static function newFactory(): MenuFactory
    {
        return MenuFactory::new();
    }

    public function items(): HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('menuItem'));
    }
}
