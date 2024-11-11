<?php

namespace Ikoncept\Fabriq\QueryBuilders;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Sorts\Sort;

class TagSort implements Sort
{
    /**
     * Sort custom columns.
     *
     * @param  mixed  $descending
     */
    public function __invoke(Builder $query, $descending, string $property): Builder
    {
        $model = Fabriq::getFqnModel(Str::singular($property));

        return $query->select([$property.'.id', DB::raw('group_concat(tags.plain_name) as name')])
            ->leftJoin('taggables', 'taggables.taggable_id', '=', $property.'.id')
            ->leftJoin('tags', 'taggables.tag_id', '=', 'tags.id')
            ->where('taggables.taggable_type', '=', $model)
            ->groupBy([$property.'.id'])
            ->orderBy('name', $descending ? 'desc' : 'asc');
    }
}
