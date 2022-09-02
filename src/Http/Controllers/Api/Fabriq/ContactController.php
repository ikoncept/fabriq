<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Ikoncept\Fabriq\Http\Requests\CreateContactRequest;
use Ikoncept\Fabriq\Http\Requests\UpdateContactRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Http\Controllers\Api\ApiController;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ContactController extends ApiController
{
    use ApiControllerTrait;

    /**
     * Returns an index of contacts.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('contact')::RELATIONSHIPS);
        $contacts = QueryBuilder::for(Fabriq::getFqnModel('contact'))
            ->allowedSorts('name', 'email', 'updated_at', 'sortindex', 'published')
            ->allowedFilters([
                AllowedFilter::scope('search'),
            ])
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($contacts, Fabriq::getTransformerFor('contact'));
    }

    public function show(Request $request, int $id): JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('contact')::RELATIONSHIPS);
        $contact = Fabriq::getFqnModel('contact')::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($contact, Fabriq::getTransformerFor('contact'));
    }

    public function store(CreateContactRequest $request): JsonResponse
    {
        $contact = Fabriq::getModelClass('contact');
        $contact->name = $request->name;
        $contact->save();

        return $this->respondWithItem($contact, Fabriq::getTransformerFor('contact'), 201);
    }

    public function update(UpdateContactRequest $request, int $id): JsonResponse
    {
        $contact = Fabriq::getFqnModel('contact')::findOrFail($id);
        $contact->fill($request->validated());

        $contact->contactTags = $request->tags;
        $contact->localizedContent = $request->localizedContent;

        $contact->updateContent([
            'image' => $request->content['image'] ?? null,
        ], $request->input('locale', app()->getLocale()));

        $contact->save();

        return $this->respondWithItem($contact, Fabriq::getTransformerFor('contact'));
    }

    public function destroy(int $id): JsonResponse
    {
        $contact = Fabriq::getFqnModel('contact')::findOrFail($id);
        $contact->delete();

        return $this->respondWithSuccess('The contact has been deleted successfully');
    }
}
