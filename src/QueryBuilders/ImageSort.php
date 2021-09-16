<?php

namespace Ikoncept\Fabriq\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class ImageSort implements Sort
{

    /**
     * Sort custom columns
     *
     * @param Builder $query
     * @param mixed $descending
     * @param string $property
     * @return Builder
     */
    public function __invoke(Builder $query, $descending, string $property) : Builder
    {
        $keyName = $property;

        return $query->select('images.*')
            ->join('media', 'media.model_id', '=', 'images.id')
            ->where('collection_name', 'images')
            ->orderBy('media.' . $keyName, $descending ? 'desc' : 'asc');
    }

}
