<?php

namespace Ikoncept\Fabriq\Transformers;

use Infab\TranslatableRevisions\Models\RevisionTemplateField;
use League\Fractal\TransformerAbstract;

class TemplateFieldTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected $availableIncludes = [
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  RevisionTemplateField  $pageTemplateField
     * @return array
     */
    public function transform(RevisionTemplateField $pageTemplateField)
    {
        return $pageTemplateField->toArray();
    }
}
