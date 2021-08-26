<?php

namespace Ikoncept\Fabriq\Transformers;

use League\Fractal\TransformerAbstract;
use Illuminate\Support\Str;

class ConfigTransformer extends TransformerAbstract
{

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Array  $config
     * @return array
     */
    public function transform(array $config)
    {
        return collect($config)->filter(function($item, $key) {
            return ! Str::contains($key, ['key']);
        })->toArray();
        // return $config;
    }
}
