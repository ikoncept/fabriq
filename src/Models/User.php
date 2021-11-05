<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\UserFactory;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasRoles;

    const RELATIONSHIPS = ['roles'];

    /**
     * Morph class
     *
     * @var string
     */
    public $morphClass = 'user';

    /**
     * Guard name
     *
     * @var string
     */
    protected $guard_name = 'web';

    /**
     * Create a new factory
     *
     * @return UserFactory
     */
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_list'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set roles according to an array
     *
     * @param array $value
     * @return void
     */
    public function setRoleListAttribute(array $value)
    {
        Fabriq::getModelClass('user')->find($this->id)->syncRoles($value);
    }

    /**
     * Search for users
     *
     * @param Builder $query
     * @param string $search
     * @return Builder
     */
    public function scopeSearch(Builder $query, string $search) : Builder
    {
        return $query->where('name', 'LIKE', '%' . $search . '%')
            ->orWhere('email', 'LIKE', '%' . $search . '%');
    }

    public function fabriqNotifications() : HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('notification'));
    }

    public function notificationsToBeNotified() : HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('notification'))
            ->whereNull('cleared_at')
            ->whereNull('notified_at');
    }

    /**
     * Display first name
     *
     * @return string
     */
    public function getFirstNameAttribute()
    {
        return Str::words($this->name, 1, '');
    }
}
