<?php

namespace Ikoncept\Fabriq\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;

class CacheBuster
{
    public function getCacheKeys(Model $model, array $cacheKeysToFlush = []): Collection
    {
        if (method_exists($model, 'getRevisionOptions') && count($cacheKeysToFlush) === 0) {
            $cacheKeysToFlush = $model->getRevisionOptions()->cacheKeysToFlush;
        }

        /** @var array<string, string> $cacheKeysToFlush */
        return collect($cacheKeysToFlush)->map(function ($tag) use ($model) {
            $parts = explode('|', $tag);
            if (isset($parts[1])) {
                $key = $parts[1];

                if ($key === 'slug' && $model->slugs) {

                    // Some models have just one slug
                    if (isset($model->slug)) {
                        return "{$parts[0]}_{$parts[1]}_{$model->slug}";
                    }

                    $slugs = collect([]);
                    foreach ($model->slugs as $slug) {
                        $slugs->push("{$parts[0]}_{$parts[1]}_{$slug->slug}");
                    }

                    return $slugs->toArray();
                }

                return "{$parts[0]}_{$parts[1]}_{$model->$key}";
            }

            return $tag;
        })->flatten();
    }
}
