<?php

namespace Ikoncept\Fabriq\Repositories\Decorators;

use Ikoncept\Fabriq\Repositories\Interfaces\MenuRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Log;

class CachingMenuRepository implements MenuRepositoryInterface
{
    /**
     * Menu repository interface
     *
     * @var MenuRepositoryInterface
     */
    protected $repository;

    /**
     * Cache repository
     *
     * @var Cache
     */
    protected $cache;

    public function __construct(MenuRepositoryInterface $repository, Cache $cache)
    {
        $this->repository = $repository;
        $this->cache = $cache;
    }


    /**
     * Find by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findBySlug(string $slug)
    {
        $locale = app()->getLocale();
        $menu = $this->cache->tags(['cms_menu_' . $slug, 'cms_menu'])
            ->rememberForever($locale, function () use ($slug) {
                Log::info('Caching menu', ['cache_key' => 'cms_menu_' . $slug, 'cms_menu']);
                return $this->repository->findBySlug($slug);
            });


        return $menu;
    }
}
