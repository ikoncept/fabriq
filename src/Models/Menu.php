<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    use HasFactory;

    const RELATIONSHIPS = [];

    public function items() : HasMany
    {
        return $this->hasMany(MenuItem::class);
    }
}
