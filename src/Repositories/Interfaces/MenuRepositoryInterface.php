<?php

namespace Ikoncept\Fabriq\Repositories\Interfaces;

interface MenuRepositoryInterface
{
    /**
     * Find by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug(string $slug);
}
