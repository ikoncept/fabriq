<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockType extends Model
{
    use HasFactory;

    protected $casts = [
        'has_children' => 'boolean'
    ];
}
