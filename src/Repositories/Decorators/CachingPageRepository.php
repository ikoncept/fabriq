<?php

namespace Ikoncept\Fabriq\Repositories\Decorators;

use Ikoncept\Fabriq\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Log;

class CachingPageRepository implements PageRepositoryInterface
{
    /**
     * Page repository interface
     *
     * @var PageRepositoryInterface
     */
    protected $repository;

    /**
     * Cache repository
     *
     * @var Cache
     */
    protected $cache;

    public function __construct(PageRepositoryInterface $repository, Cache $cache)
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
        $locale = 'sv';
        $page = $this->cache->tags(['cms_pages', 'cms_page_' . $slug])
            ->rememberForever($locale, function () use ($slug) {
                Log::info('Caching page', ['cache_key' => 'cms_page_' . $slug, 'cms_page']);
                return $this->repository->findBySlug($slug);
            });

        return $page;
    }

    /**
     * Find preview by slug
     *
     * @param string $slug
     * @return mixed
     */
    public function findPreviewBySlug(string $slug)
    {
        $locale = 'sv';
        $page = $this->cache->tags(['cms_page_' . $slug, 'cms_page'])
            ->rememberForever($locale, function () use ($slug) {
                Log::info('Caching page', ['cache_key' => 'cms_page_' . $slug, 'cms_page']);
                return $this->repository->findPreviewBySlug($slug);
            });

        return $page;
    }
}
