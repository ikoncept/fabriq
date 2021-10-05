<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use Infab\TranslatableRevisions\Models\I18nLocale;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class PageTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'content', 'template', 'slugs',
        'localizedContent', 'children'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Model  $page
     * @return array
     */
    public function transform(Model $page) : array
    {
        return $page->toArray();
    }

    /**
     * Include content
     *
     * @param Model $page
     * @return Item
     */
    public function includeContent(Model $page) : Item
    {
        $content = $page->getFieldContent($page->revision);

        return $this->item($content, new ContentTransformer());
    }

    /**
     * Include page template
     *
     * @param Model $page
     * @return Item
     */
    public function includeTemplate(Model $page) : Item
    {
        return $this->item($page->template, new TemplateTransformer());
    }

    /**
     * Include slugs
     *
     * @param Model $page
     * @return Collection
     */
    public function includeSlugs(Model $page) : Collection
    {
        return $this->collection($page->slugs, new SlugTransformer());
    }

    public function includeLocalizedContent(Model $page) : Item
    {

        $enabledLocales = I18nLocale::where('enabled', 1)
            ->select('iso_code')
            ->orderBy('id', 'desc')->get();

        return $this->item($enabledLocales, new LocaleContentTransformer($page));
    }

    public function includeChildren(Model $page) : Collection
    {
        return $this->collection($page->children, new  PageTransformer);
    }
}
