<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Models\User;
use Illuminate\Support\Collection as IlluminateCollection;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\NullResource;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included.
     *
     * @var array
     */
    protected array $availableIncludes = [
        'roles', 'invitation',
    ];

    protected array $defaultIncludes = [
        'image',
    ];

    /**
     * Transform the given object
     * to the required format.
     *
     * @param  User  $user
     * @return array
     */
    public function transform(User $user)
    {
        return [
            'id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'role_list' => $this->getRoles($user),
            'timezone' => 'Europe/Stockholm',
            // 'email_verified_at' => ($user->email_verified_at) ? $user->email_verified_at->toISOString()  : false,
            'email_verified_at' => ($user->email_verified_at) ? (string) $user->email_verified_at : null,
            'updated_at' => $user->updated_at,
        ];
    }

    /**
     * Include roles.
     *
     * @param  User  $user
     * @return Collection
     */
    public function includeRoles(User $user): Collection
    {
        return $this->collection($user->roles, Fabriq::getTransformerFor('role'));
    }

    /**
     * Include image.
     *
     * @param  User  $user
     * @return Item
     */
    public function includeImage(User $user): Item
    {
        return $this->item($user->image, new UserImageTransformer());
    }

    public function includeInvitation(User $user): Item|NullResource
    {
        if (! $user->invitation) {
            return $this->null();
        }

        return $this->item($user->invitation, new InvitationTransformer);
    }

    /**
     * Get roles.
     *
     * @param  User  $user
     * @return IlluminateCollection
     */
    public function getRoles(User $user): IlluminateCollection
    {
        if (auth()->user()->id == $user->id) {
            return $user->roles->pluck('name');
        }

        return $user->roles->pluck('name');
    }
}
