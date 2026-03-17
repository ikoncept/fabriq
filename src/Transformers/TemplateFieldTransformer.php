<?php

namespace Ikoncept\Fabriq\Transformers;

use Karabin\TranslatableRevisions\Models\RevisionTemplateField;
use League\Fractal\TransformerAbstract;

class TemplateFieldTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     */
    protected array $availableIncludes = [
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @return array
     */
    public function transform(RevisionTemplateField $pageTemplateField)
    {
        return $pageTemplateField->toArray();
    }
}
