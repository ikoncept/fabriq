<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\Page;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     */
    protected array $availableIncludes = [
        'content', 'template', 'slugs',
        'localizedContent', 'children',
    ];

    /**
     * Transform the given object
     * to the required format.
     */
    public function transform(Page $page): array
    {
        return $page->toArray();
    }

    /**
     * Include content.
     */
    public function includeContent(Page $page): Item
    {
        $content = $page->getSimpleFieldContent($page->revision);

        return $this->item($content, new ContentTransformer);
    }

    /**
     * Include page template.
     */
    public function includeTemplate(Page $page): Item
    {
        return $this->item($page->template, new TemplateTransformer);
    }

    /**
     * Include slugs.
     */
    public function includeSlugs(Page $page): Collection
    {
        return $this->collection($page->slugs, Fabriq::getTransformerFor('slug'));
    }

    public function includeLocalizedContent(Page $page): Item
    {
        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($page));
    }

    public function includeChildren(Page $page): Collection
    {
        return $this->collection($page->children, new self);
    }
}
