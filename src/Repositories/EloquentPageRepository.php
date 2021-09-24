<?php

namespace Ikoncept\Fabriq\Repositories;

use Ikoncept\Fabriq\Models\Page;
use Ikoncept\Fabriq\Models\Slug;
use Ikoncept\Fabriq\Repositories\Interfaces\PageRepositoryInterface;

class EloquentPageRepository implements PageRepositoryInterface
{
    /**
     * Page model
     *
     * @var Page
     */
    private $model;

    public function __construct(Page $model, Slug $slugModel)
    {
        $this->model = $model;
        $this->slugModel = $slugModel;
    }

    /**
     * Find by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug(string $slug)
    {
        $model = $this->model->whereHas('slugs', function($query) use ($slug) {
            $query->where('slug', $slug);
        })->firstOrFail();

        // Decorate with content
        $model->content = $model->getFieldContent($model->published_version);
        $model->slug = $slug;

        return $model;
    }

    public function findPreviewBySlug(string $slug)
    {
        $model = $this->model->whereHas('slugs', function($query) use ($slug) {
            $query->where('slug', $slug);
        })->firstOrFail();

        // Decorate with content
        $model->content = $model->getFieldContent($model->revision);
        $model->slug = $slug;

        return $model;
    }
}