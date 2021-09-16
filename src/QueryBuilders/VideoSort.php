<?php

namespace Ikoncept\Fabriq\QueryBuilders;

use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\Sorts\Sort;

class VideoSort implements Sort
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

        return $query->select('videos.*')
            ->join('media', 'media.model_id', '=', 'videos.id')
            ->where('collection_name', 'videos')
            ->orderBy('media.' . $keyName, $descending ? 'desc' : 'asc');
    }

}
