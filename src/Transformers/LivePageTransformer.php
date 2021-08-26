<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\Page;
use League\Fractal\TransformerAbstract;

class LivePageTransformer extends TransformerAbstract
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
     * Transform the given object
     * to the required format
     *
     * @param  Page  $page
     * @return array
     */
    public function transform(Page $page)
    {
        $pageData = [
            'name' => $page->name,
            'slug' => $page->slug,
            'template_id' => $page->template_id,
            'updated_at' => $page->updated_at,
        ];
        $content = $page->content;

        return array_merge($pageData, ['content' => ['data' => $content]]);
    }
}
