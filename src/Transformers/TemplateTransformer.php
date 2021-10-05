<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class TemplateTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'fields', 'groupedFields'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param Model $template
     * @return array
     */
    public function transform(Model $template) : array
    {
        return $template->toArray();
    }

    /**
     * Include template fields
     *
     * @param Model $template
     * @return Collection
     */
    public function includeFields(Model $template) : Collection
    {
        return $this->collection($template->fields, new TemplateFieldTransformer());
    }

    /**
     * Return grouped fields
     *
     * @param Model $template
     * @return Item
     */
    public function includeGroupedFields(Model $template) : Item
    {
        $grouped = $template->fields->groupBy('group');

        return $this->item($grouped, new GroupedTemplateFieldTransformer());
    }
}
