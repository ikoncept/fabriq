<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Carbon\CarbonImmutable;
use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreateEventRequest;
use Ikoncept\Fabriq\Models\Event;
use Ikoncept\Fabriq\Services\CalendarService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class EventController extends ApiController
{
    use ApiControllerTrait;

    public function index(Request $request): JsonResponse
    {
        $events = QueryBuilder::for(Fabriq::getFqnModel('event'))
            ->allowedFilters(AllowedFilter::scope('dateRange'))
            ->paginate($this->number);

        $end = CarbonImmutable::parse(explode(',', $request->filter['dateRange'])[1]);
        $computedEvents = CalendarService::getComputedDailyIntervals($events, $end);

        $mergedEvents = $events->toBase()->merge($computedEvents);

        return $this->respondWithCollection($mergedEvents, Fabriq::getTransformerFor('event'));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $event = Event::where('id', $id)->firstOrFail();

        return $this->respondWithItem($event, Fabriq::getTransformerFor('event'));
    }

    public function store(CreateEventRequest $request): JsonResponse
    {
        $event = new Event;
        $event->fill($request->validated());
        $event->save();

        foreach ($request['localizedContent'] as $locale => $content) {
            $event->updateContent($content, $locale);
        }

        return $this->respondWithItem($event, Fabriq::getTransformerFor('event'), 201);
    }

    public function update(CreateEventRequest $request, int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->fill($request->validated());
        $event->save();

        foreach ($request['localizedContent'] as $locale => $content) {
            $event->updateContent($content, $locale);
        }

        return $this->respondWithItem($event, Fabriq::getTransformerFor('event'));
    }

    public function destroy(int $id): JsonResponse
    {
        $event = Event::findOrFail($id);
        $event->delete();

        return $this->respondWithSuccess('Event was deleted succesfully');
    }
}
