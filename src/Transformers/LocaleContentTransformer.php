<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Support\Collection;
use League\Fractal\TransformerAbstract;

class LocaleContentTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     */
    protected array $availableIncludes = [
    ];

    /**
     * The content model.
     *
     * @var mixed
     */
    protected $contentModel;

    /**
     * @param  mixed  $contentModel
     */
    public function __construct($contentModel)
    {
        $this->contentModel = $contentModel;
    }

    /**
     * Transform.
     */
    public function transform(Collection $locales): array
    {
        $localeContent = $locales->mapWithKeys(function ($locale) {
            $content = $this->contentModel->getSimpleFieldContent($this->contentModel->revision, $locale->iso_code);

            return [
                $locale->iso_code => ['content' => $content],
            ];
        });

        return $localeContent->toArray();
    }
}
