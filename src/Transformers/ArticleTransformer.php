<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Article;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected $availableIncludes = [
        'content', 'template', 'slugs',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  Article  $article
     * @return array
     */
    public function transform(Article $article)
    {
        return [
            'id' => (int) $article->id,
            'name' => (string) $article->name,
            'slug' => (string) $article->slug,
            'is_published' => (bool) $article->is_published,
            'publishes_at' => (string) ($article->publishes_at) ? $article->publishes_at->toISOString() : '',
            'publishes_at_date' => (string) ($article->publishes_at) ? $article->publishes_at->toDateString() : '',
            'unpublishes_at' => (string) ($article->unpublishes_at) ? $article->unpublishes_at->toISOString() : '',
            'has_unpublished_time' => (bool) $article->has_unpublished_time,
            'updated_at' => (string) $article->updated_at->toISOString(),
            'created_at' => (string) $article->created_at->toISOString(),
        ];
    }

    /**
     * Include content.
     *
     * @param Article $article
     * @return Item
     */
    public function includeContent(Article $article) : Item
    {
        $content = $article->getFieldContent($article->revision);

        return $this->item($content, new ContentTransformer());
    }

    /**
     * Include template.
     *
     * @param Article $article
     * @return Item
     */
    public function includeTemplate(Article $article) : Item
    {
        return $this->item($article->template, new TemplateTransformer());
    }

    /**
     * Include slugs.
     *
     * @param Article $article
     * @return Collection
     */
    public function includeSlugs(Article $article) : Collection
    {
        return $this->collection($article->slugs, new SlugTransformer());
    }
}
