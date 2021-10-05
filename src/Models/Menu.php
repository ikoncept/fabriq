<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\MenuFactory;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    const RELATIONSHIPS = [];

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'menu';

    protected static function newFactory() : MenuFactory
    {
        return MenuFactory::new();
    }

    public function items() : HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('menuItem'));
    }
}
