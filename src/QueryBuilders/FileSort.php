<?php

namespace Ikoncept\Fabriq\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class FileSort implements Sort
{
    /**
     * Sort custom columns.
     *
     * @param  Builder  $query
     * @param  mixed  $descending
     * @param  string  $property
     * @return Builder
     */
    public function __invoke(Builder $query, $descending, string $property): Builder
    {
        $keyName = $property;

        return $query->select('files.*')
            ->join('media', 'media.model_id', '=', 'files.id')
            ->where('collection_name', 'files')
            ->orderBy('media.'.$keyName, $descending ? 'desc' : 'asc');
    }
}
