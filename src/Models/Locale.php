<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Locale extends Model
{

    protected $table = 'i18n_locales';

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'locale';

    public function scopeEnabled(Builder $query) : Builder
    {
        return $query->where('enabled', 1);
    }
}
