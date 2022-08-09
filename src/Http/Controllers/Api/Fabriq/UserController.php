<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreateUserRequest;
use Ikoncept\Fabriq\Http\Requests\UpdateUserRequest;
use Ikoncept\Fabriq\Models\User;
use Ikoncept\Fabriq\Transformers\UserTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Returns an index of users.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(User::RELATIONSHIPS);
        $paginator = QueryBuilder::for(Fabriq::getFqnModel('user'))
            ->allowedSorts('name', 'email', 'id', 'updated_at')
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->with('roles')
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($paginator, new UserTransformer);
    }

    public function store(CreateUserRequest $request) : JsonResponse
    {
        $user = Fabriq::getModelClass('user');
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = bcrypt(Str::random(12));
        $user->save();
        $user->syncRoles($request->role_list);

        return $this->respondWithItem($user, new UserTransformer, 201);
    }

    /**
     * Show a single user.
     *
     * @param Request $request
     * @param int $id
     * @return JsonResponse
     */
    public function show(Request $request, int $id) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(User::RELATIONSHIPS);
        $user = Fabriq::getModelClass('user')->with($eagerLoad)
            ->with('roles')
            ->findOrFail($id);

        return $this->respondWithItem($user, new UserTransformer);
    }

    public function update(UpdateUserRequest $request, int $id) : JsonResponse
    {
        $user = Fabriq::getModelClass('user')->findOrFail($id);
        $user->fill($request->validated());
        $user->save();

        return $this->respondWithItem($user, new UserTransformer);
    }

    public function destroy(Request $request, int $id) : JsonResponse
    {
        if ($id === $request->user()->id) {
            return $this->errorWrongArgs('Du kan inte radera dig själv');
        }
        $user = User::findOrFail($id);
        $user->delete();

        return $this->respondWithSuccess('The user has been deleted');
    }
}
