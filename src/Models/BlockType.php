<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\BlockTypeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockType extends Model
{
    use HasFactory;

    /**
     * Create a new factory
     */
    protected static function newFactory() : BlockTypeFactory
    {
        return BlockTypeFactory::new();
    }


    protected $casts = [
        'has_children' => 'boolean'
    ];
}
