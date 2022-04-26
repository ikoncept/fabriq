<?php

namespace Ikoncept\Fabriq\Concerns;

use Ikoncept\Fabriq\Fabriq;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Expression;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;

trait HasPaths
{

    public function scopeWhereHash(Builder $query, string $hash) : Builder
    {
        return $query->where($this->getWhereHashStatement(), $hash);
    }

    /**
     * Get the absolute path
     *
     * @param string|null $path
     * @return string
     */
    public function getAbsolutePath($path) : string
    {
        return config('fabriq.front_end_domain')
            . $this->currentLocaleString()
            . ($path ??  $this->latestSlug->slug);
    }

    public function getPermalinkPath() : string
    {
        return config('fabriq.front_end_domain')
            . '/permalink/'
            . hash('md5', (string) $this->id)
            . $this->currentLocaleString();
    }

    public function transformPaths() : array
    {
        return $this->paths->map(function($item) {
            if(!isset($item[App::currentLocale()])) {
                return null;
            }
            return [
                'absolute_path' => $this->getAbsolutePath($item[App::currentLocale()]['0']),
                'permalink' => $this->getPermalinkPath()
            ];
        })->filter()->first();
    }

    protected function currentLocaleString() : string
    {
        $enabledLocales = Fabriq::getModelClass('locale')->cachedLocales();
        if($enabledLocales->count() > 1) {
            return '/' . App::getLocale();
        }

        return '';
    }

    protected function getWhereHashStatement() : Expression
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");

        return ($driver === 'pgsql') ? DB::raw('md5(id::text)') : DB::raw('md5(id)');
    }
}
