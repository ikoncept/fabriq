<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Models\Role as SpatieRole;

class Role extends SpatieRole
{
    use HasFactory;

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'role';

    public function scopeNotHidden(Builder $query) : Builder
    {
        return $query->where('hidden', 0);
    }
}
