<?php

namespace Ikoncept\Fabriq\Transformers;

use Illuminate\Database\Eloquent\Model;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class ArticleTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'content', 'template', 'slugs'
    ];

    /**
     * Transform the given object
     * to the required format
     *
     * @param  Model  $article
     * @return array
     */
    public function transform(Model $article)
    {
        return [
            'id' => (int) $article->id,
            'name' => (string) $article->name,
            'slug' => (string) $article->slug,
            'is_published' => (bool) $article->is_published,
            'publishes_at' => (string) ($article->publishes_at) ? $article->publishes_at->toISOString() : '',
            'unpublishes_at' => (string) ($article->unpublishes_at) ? $article->unpublishes_at->toISOString() : '',
            'has_unpublished_time' => (bool) $article->has_unpublished_time,
            'updated_at' => (string) $article->updated_at->toISOString(),
            'created_at' => (string) $article->created_at->toISOString()
        ];
    }

    /**
     * Include content
     *
     * @param Model $article
     * @return Item
     */
    public function includeContent(Model $article) : Item
    {
        $content = $article->getFieldContent($article->revision);

        return $this->item($content, new ContentTransformer());
    }

    /**
     * Include page template
     *
     * @param Model $article
     * @return Item
     */
    public function includeTemplate(Model $article) : Item
    {
        return $this->item($article->template, new TemplateTransformer());
    }
}
