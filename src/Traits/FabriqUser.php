<?php

namespace Ikoncept\Fabriq\Traits;

use Ikoncept\Fabriq\Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Str;
use Spatie\Permission\Traits\HasRoles;

trait FabriqUser
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * Guard name.
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * Create a new factory.
     *
     * @return UserFactory
     */
    // protected static function newFactory()
    // {
    //     return UserFactory::new();
    // }

    /**
     * Set roles according to an array.
     *
     * @param array $value
     * @return void
     */
    public function setRoleListAttribute(array $value)
    {
        $this->syncRoles($value);
    }

    /**
     * Search for users.
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->where('name', 'LIKE', '%'.$search.'%')
            ->orWhere('email', 'LIKE', '%'.$search.'%');
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(Notification::class);
    }

    public function notificationsToBeNotified(): HasMany
    {
        return $this->hasMany(Notification::class)
            ->whereNull('cleared_at')
            ->whereNull('notified_at');
    }

    /**
     * Display first name.
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return Str::words($this->name, 1, '');
    }
}
