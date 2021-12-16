<?php

namespace Ikoncept\Fabriq\Transformers;

use Ikoncept\Fabriq\Models\User;
use Illuminate\Support\Collection as IlluminateCollection;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\Item;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    /**
     * Determines which objects
     * that can be included
     *
     * @var array
     */
    protected $availableIncludes = [
        'roles',
    ];

    protected $defaultIncludes = [
        'image'
    ];

    /**
     * Transform the given object
     * to the required format
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
            'email_verified_at' => ($user->email_verified_at) ? $user->email_verified_at->toISOString()  : false,
            'updated_at' => $user->updated_at
        ];
    }

    /**
     * Include roles
     *
     * @param User $user
     * @return Collection
     */
    public function includeRoles(User $user) : Collection
    {
        return $this->collection($user->roles, new RoleTransformer);
    }

    /**
     * Include image
     *
     * @param User $user
     * @return Item
     */
    public function includeImage(User $user) : Item
    {
        return $this->item($user->image, new UserImageTransformer());
    }

    /**
     * Get roles
     *
     * @param User $user
     * @return IlluminateCollection
     */
    public function getRoles(User $user) : IlluminateCollection
    {
        if(auth()->user()->id == $user->id) {
            return $user->roles->pluck('name');
        }

        return $user->roles->pluck('name');
    }
}
