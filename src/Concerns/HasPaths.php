<?php

namespace Ikoncept\Fabriq\Concerns;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\MenuItem;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait HasPaths
{
    public function scopeWhereHash(Builder $query, string $hash): Builder
    {
        return $query->where($this->getWhereHashStatement(), $hash);
    }

    /**
     * Get the absolute path.
     *
     * @param  string|null  $path
     */
    public function getAbsolutePath($path): string
    {
        return config('fabriq.front_end_domain')
            .$this->currentLocaleString()
            .$path;
    }

    public function getPermalinkPath(): string
    {
        return config('fabriq.front_end_domain')
            .'/permalink/'
            .hash('md5', (string) $this->id)
            .$this->currentLocaleString();
    }

    public function transformPaths(): array
    {
        return $this->paths->map(function ($item) {
            if (! isset($item[App::currentLocale()])) {
                return null;
            }

            return [
                'absolute_path' => $this->getAbsolutePath($item[App::currentLocale()]['0'] ?? null),
                'permalink' => $this->getPermalinkPath(),
            ];
        })->filter()->first();
    }

    protected function currentLocaleString(): string
    {
        $enabledLocales = Fabriq::getModelClass('locale')->cachedLocales();
        if ($enabledLocales->count() > 1) {
            return '/'.App::getLocale();
        }

        return '';
    }

    protected function getWhereHashStatement(): mixed
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        return ($driver === 'pgsql') ? DB::raw('md5(id::text)') : DB::raw('md5(id)');
    }

    public function getPathsAttribute(): Collection
    {
        $slugGroups = collect([]);

        $supportedLocales = Fabriq::getModelClass('locale')->cachedLocales();

        if ($this->menuItems !== null && $this->menuItems->count()) {
            foreach ($supportedLocales as $locale => $item) {
                $localizedSlugs = $this->menuItems->map(function ($item) use ($locale) {
                    if (! $item->ancestors->count()) {
                        return '';
                    }

                    return collect($item->ancestors)->reduce(function ($carry, $subItem) use ($locale) {
                        /** @var MenuItem $subItem * */
                        if (! $subItem->page) {
                            return;
                        }

                        return $carry.'/'.$subItem->getSlugString($locale);
                    }, '').'/'.$item->getSlugString($locale);
                })->unique();

                $slugGroups->push([$locale => $localizedSlugs]);
            }

            return $slugGroups;
        }

        foreach ($supportedLocales as $locale => $item) {
            $slugGroups->push([
                $locale => $this->slugs->where('locale', $locale)->pluck('slug')->map(function ($item) {
                    return '/'.$item;
                }),
            ]);
        }

        return $slugGroups;

    }

    public function getLocalizedPathsAttribute(): Collection
    {
        $slugGroups = collect([]);
        $localizedSlugs = $this->menuItems->map(function ($item) {
            if (! $item->ancestors->count()) {
                return '';
            }

            return collect($item->ancestors)->reduce(function ($carry, $subItem) {
                /** @var MenuItem $subItem * */
                if (! $subItem->page) {
                    return;
                }

                return $carry.'/'.$subItem->getSlugString();
            }, '').'/'.$item->getSlugString();
        })->unique();
        $slugGroups->push($localizedSlugs);

        // If no menu items exists, fetch the latest slug
        if (! $slugGroups[0]->count()) {
            $slugGroups[0]->push(['/'.$this->latestSlug->slug]);
        }

        return $slugGroups;
    }
}
