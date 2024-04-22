<?php

namespace Ikoncept\Fabriq\Repositories\Decorators;

use Ikoncept\Fabriq\Repositories\Interfaces\PageRepositoryInterface;
use Illuminate\Contracts\Cache\Repository as Cache;
use Illuminate\Support\Facades\Log;

class CachingPageRepository implements PageRepositoryInterface
{
    /**
     * Page repository interface.
     *
     * @var PageRepositoryInterface
     */
    protected $repository;

    /**
     * Cache repository.
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
     * Find by slug.
     *
     * @return mixed
     */
    public function findBySlug(string $slug)
    {
        $locale = 'sv';
        $page = $this->cache->tags(['fabriq_pages', 'fabriq_page_'.$slug])
            ->rememberForever($locale, function () use ($slug) {
                Log::info('Caching page', ['cache_key' => 'fabriq_page_'.$slug, 'fabriq_page']);

                return $this->repository->findBySlug($slug);
            });

        return $page;
    }

    /**
     * Find preview by slug.
     *
     * @return mixed
     */
    public function findPreviewBySlug(string $slug)
    {
        $locale = 'sv';
        $page = $this->cache->tags(['fabriq_page_'.$slug, 'fabriq_page'])
            ->rememberForever($locale, function () use ($slug) {
                Log::info('Caching page', ['cache_key' => 'fabriq_page_'.$slug, 'fabriq_page']);

                return $this->repository->findPreviewBySlug($slug);
            });

        return $page;
    }

    /**
     * Find pages by ids.
     *
     * @return mixed
     */
    public function findByIds(array $ids)
    {
        $pages = $this->cache->tags(['fabriq_page'])
            ->rememberForever(base64_encode(implode('-', $ids)), function () use ($ids) {
                return $this->repository->findByIds($ids);
            });

        return $pages;
    }
}
