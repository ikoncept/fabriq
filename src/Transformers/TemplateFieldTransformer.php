<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\TransformerAbstract;

class TemplateFieldTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Model  $pageTemplateField
     * @return array
     */
    public function transform(Model $pageTemplateField)
    {
        return $pageTemplateField->toArray();
    }
}
