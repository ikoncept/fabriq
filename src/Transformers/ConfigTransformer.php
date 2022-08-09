<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Support\Str;
use League\Fractal\TransformerAbstract;

class ConfigTransformer extends TransformerAbstract
{
    /**
     * Transform the given object
     * to the required format.
     *
     * @param  array  $config
     * @return array
     */
    public function transform(array $config)
    {
        return collect($config)->filter(function ($item, $key) {
            return ! Str::contains($key, ['key']);
        })->toArray();
        // return $config;
    }
}
