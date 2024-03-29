<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\ClearNotificationRequest;
use Ikoncept\Fabriq\Models\Notification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $request): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('notification')::RELATIONSHIPS);
        $notifications = QueryBuilder::for(Fabriq::getFqnModel('notification'))
            ->allowedFilters([
                AllowedFilter::scope('unseen'),
                AllowedFilter::scope('seen'),
            ])
            ->where('user_id', $request->user()->id)
            ->with($eagerLoad)
            ->orderBy('created_at', 'desc')
            ->paginate($this->number);

        return $this->respondWithPaginator($notifications, Fabriq::getTransformerFor('notification'));
    }

    public function update(ClearNotificationRequest $request, int $id): JsonResponse
    {
        $notification = Notification::findOrFail($id);
        $notification->cleared_at = now();

        $notification->save();

        return $this->respondWithItem($notification, Fabriq::getTransformerFor('notification'));
    }
}
