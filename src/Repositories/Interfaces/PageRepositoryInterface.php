<?php

namespace Ikoncept\Fabriq\Repositories\Interfaces;

interface PageRepositoryInterface
{
    /**
     * Find by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug(string $slug);

    /**
     * Find a preview by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findPreviewBySlug(string $slug);
}
