<?php

namespace Ikoncept\Fabriq\Models;

use Ikoncept\Fabriq\Database\Factories\UserFactory;
use Ikoncept\Fabriq\Fabriq;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, HasRoles, Notifiable;

    public const RELATIONSHIPS = ['roles'];

    /**
     * Morph class.
     *
     * @var string
     */
    public $morphClass = 'user';

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
    protected static function newFactory()
    {
        return UserFactory::new();
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_list',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Set roles according to an array.
     *
     * @return void
     */
    public function setRoleListAttribute(array $value)
    {
        Fabriq::getModelClass('user')->find($this->id)->syncRoles($value);
    }

    public function invitation(): HasOne
    {
        return $this->hasOne(Invitation::class)->latestOfMany();
    }

    /**
     * Create a new invitation.
     *
     * @param  int|null  $invitedBy
     */
    public function createInvitation($invitedBy = null): Invitation
    {
        $invitation = Invitation::create([
            'user_id' => $this->id,
            'invited_by' => $invitedBy ?? auth()->user()->id,
        ]);

        return $invitation;
    }

    /**
     * Search for users.
     */
    public function scopeSearch(Builder $query, string $search): Builder
    {
        return $query->whereLike(['name', 'email'], $search);
    }

    public function fabriqNotifications(): HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('notification'));
    }

    public function notificationsToBeNotified(): HasMany
    {
        return $this->hasMany(Fabriq::getFqnModel('notification'))
            ->whereNull('cleared_at')
            ->whereNull('notified_at');
    }

    public function image(): BelongsTo
    {
        return $this->belongsTo(Image::class);
    }

    public function getFirstNameAttribute(): string
    {
        return explode(' ', $this->name, 2)[0];
    }

    public function getLastNameAttribute(): string
    {
        $parts = explode(' ', $this->name);

        return array_pop($parts);
    }

    public function receivesBroadcastNotificationsOn(): array
    {
        $channel = config('fabriq.ws_prefix').'.user.'.$this->id;

        return [new Channel($channel)];
    }
}
