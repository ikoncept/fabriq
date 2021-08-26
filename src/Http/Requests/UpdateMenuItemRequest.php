<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateMenuItemRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'content.title' => 'required_if:item.type,external|max:255',
            'content.external_url' => 'required_if:item.type,external|max:255',
            'item.page_id' => 'required_if:item.type,internal|nullable',
            'item.type' => 'required',
            'content' => 'array',
            // 'is_external' => 'boolean',
            // 'external_url' => 'required_if:is_external,1|url|nullable',
            // 'redirect' => 'boolean',
            // 'redirect_url' => 'required_if:redirect,1|url|nullable',
            // 'permanent_redirect' => 'boolean',
            // 'interactive' => 'boolean',
        ];
    }
}
