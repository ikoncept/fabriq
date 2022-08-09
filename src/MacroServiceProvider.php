<?php

namespace Ikoncept\Fabriq;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Str;

class MacroServiceProvider extends ServiceProvider
{
    /**
     * Like statmement
     *
     * @var string
     */
    protected $likeStatement;

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->setLikeStatement();
        $likeStatement = $this->likeStatement;

        Builder::macro('whereLike', function ($attributes, string $searchTerm) use ($likeStatement) {
            /** @var Builder $this **/
            $this->where(function (Builder $query) use ($attributes, $searchTerm, $likeStatement) {
                foreach (Arr::wrap($attributes) as $attribute) {
                    $query->when(
                        Str::contains($attribute, '.'),
                        function (Builder $query) use ($attribute, $searchTerm, $likeStatement) {
                            [$relationName, $relationAttribute] = explode('.', $attribute);
                            $query->orWhereHas($relationName, function (Builder $query) use ($relationAttribute, $searchTerm, $likeStatement) {
                                $query->where($relationAttribute, $likeStatement, "%{$searchTerm}%");
                            });
                        },
                        function (Builder $query) use ($attribute, $searchTerm, $likeStatement) {
                            $query->orWhere($attribute, $likeStatement, "%{$searchTerm}%");
                        }
                    );
                }
            });
            return $this;
        });
    }

    protected function setLikeStatement() : void
    {
        $connection = config('database.default');
        $driver = config("database.connections.{$connection}.driver");
        $this->likeStatement =  ($driver === 'pgsql') ? 'ILIKE' : 'LIKE';
    }
}
