<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\MenuFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    const RELATIONSHIPS = [];

    protected static function newFactory() : MenuFactory
    {
        return MenuFactory::new();
    }

    public function items() : HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
