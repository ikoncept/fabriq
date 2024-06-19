<?php

namespace Ikoncept\Fabriq\Helpers;

use Illuminate\Support\Collection;

class RecursiveArrayValues
{
    public static function fromCollection(Collection $collection, ?array $keys = null, bool $unique = true): array
    {
        $arr = $collection->toArray();
        array_walk_recursive($arr, function ($v, $k) use ($keys, &$val) {
            if (in_array($k, $keys)) {
                $val[] = strip_tags($v);
            }
        });

        return $unique ? array_unique($val ?? []) : $val ?? [];
    }
}
