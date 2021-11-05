<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Support\Collection;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\TransformerAbstract;

class LocaleContentTransformer extends TransformerAbstract
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
     * The content model
     *
     * @var mixed
     */
    protected $contentModel;

    /**
     *
     * @param mixed $contentModel
     */
    public function __construct($contentModel)
    {
        $this->contentModel = $contentModel;
    }

    /**
     * Transform
     *
     * @param Collection $locales
     * @return array
     */
    public function transform(Collection $locales) : array
    {
        $localeContent = $locales->mapWithKeys(function ($locale) {
            $content = $this->contentModel->getFieldContent($this->contentModel->revision, $locale->iso_code);
            return [
                $locale->iso_code => ['content' => $content]
            ];
        });

        return $localeContent->toArray();
    }
}
