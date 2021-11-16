<?php

namespace Ikoncept\Fabriq\ContentGetters;

use Illuminate\Database\Eloquent\Builder;

class BaseGetter
{
    protected static function getHash(Builder $query) : string
    {
        $bindings = json_encode($query->getBindings());
        $sql = json_encode($query->toSql());
        $hash = hash('adler32', $bindings.$sql);

        return $hash;
    }

    public static function getObjectOnce(string $hash, Builder $builder) : object|null
    {
        return once(function () use ($hash, $builder) {
            return $builder->first();
        });
    }
}
