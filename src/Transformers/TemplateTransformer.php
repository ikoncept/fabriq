<?php

namespace Ikoncept\Fabriq\Transformers;

use Infab\TranslatableRevisions\Models\RevisionTemplate;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class TemplateTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'fields', 'groupedFields',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  RevisionTemplate  $template
     * @return array
     */
    public function transform(RevisionTemplate $template): array
    {
        return $template->toArray();
    }

    /**
     * Include template fields.
     *
     * @param RevisionTemplate $template
     * @return Collection
     */
    public function includeFields(RevisionTemplate $template): Collection
    {
        return $this->collection($template->fields, new TemplateFieldTransformer());
    }

    /**
     * Return grouped fields.
     *
     * @param RevisionTemplate $template
     * @return Item
     */
    public function includeGroupedFields(RevisionTemplate $template): Item
    {
        $grouped = $template->fields->groupBy('group');

        return $this->item($grouped, new GroupedTemplateFieldTransformer());
    }
}
