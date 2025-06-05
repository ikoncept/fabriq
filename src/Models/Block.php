<?php

namespace Ikoncept\Fabriq\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\EloquentSortable\Sortable;
use Spatie\EloquentSortable\SortableTrait;

class Block extends Model implements Sortable
{
    use HasFactory;
    use SortableTrait;

    public $sortable = [
        'order_column_name' => 'sortindex',
        'sort_when_creating' => true,
    ];

    protected $fillable = ['page_id', 'revision', 'locale'];

    protected $casts = [
        'content' => 'object',
    ];

    public function children()
    {
        return $this->hasMany(Block::class, 'parent_id');
    }

    public function blockType()
    {
        return $this->belongsTo(BlockType::class);
    }

    public function buildSortQuery()
    {
        return static::query()->where('locale', $this->locale);
    }
}
