<?php

namespace Ikoncept\Fabriq\Http\Controllers\Api\Fabriq;

use Ikoncept\Fabriq\Fabriq;
use Infab\Core\Http\Controllers\Api\ApiController;
use Ikoncept\Fabriq\Http\Requests\CreateContactRequest;
use Ikoncept\Fabriq\Http\Requests\UpdateContactRequest;
use Ikoncept\Fabriq\Models\Contact;
use Ikoncept\Fabriq\Transformers\ContactTransformer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Infab\Core\Traits\ApiControllerTrait;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class ContactsController extends ApiController
{

    use ApiControllerTrait;

    /**
     * Returns an index of contacts
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('contact')::RELATIONSHIPS);
        $contacts = QueryBuilder::for(Fabriq::getFqnModel('contact'))
            ->allowedSorts('name', 'email', 'updated_at', 'sortindex')
            ->allowedFilters([
                AllowedFilter::scope('search')
            ])
            ->allowedAppends(['image'])
            ->with($eagerLoad)
            ->paginate($this->number);

        return $this->respondWithPaginator($contacts, new ContactTransformer);
    }

    public function show(Request $request, int $id) : JsonResponse
    {
        $eagerLoad = $this->getEagerLoad(Fabriq::getFqnModel('contact')::RELATIONSHIPS);
        $contact = Contact::where('id', $id)
            ->with($eagerLoad)
            ->firstOrFail();

        return $this->respondWithItem($contact, new ContactTransformer);
    }

    public function store(CreateContactRequest $request) : JsonResponse
    {
        $contact = new Contact;
        $contact->name = $request->name;
        $contact->save();

        return $this->respondWithItem($contact, new ContactTransformer, 201);
    }


    public function update(UpdateContactRequest $request, int $id) : JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->fill($request->validated());

        $contact->contactTags = $request->tags;
        $contact->localizedContent = $request->localizedContent;

        $contact->updateContent([
            'image' => $request->content['image'] ?? null
        ],$request->input('locale', app()->getLocale()));

        $contact->save();

        return $this->respondWithItem($contact, new ContactTransformer);
    }

    public function destroy(int $id) : JsonResponse
    {
        $contact = Contact::findOrFail($id);
        $contact->delete();

        return $this->respondWithSuccess('The contact has been deleted successfully');
    }
}
