<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBlockTypeRequest extends FormRequest
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
            'name' => ['required', 'max:255'],
            'component_name' => ['required', 'max:255'],
            'base_64_svg' => ['string', 'nullable'],
            'has_children' => ['boolean'],
            'options' => ['array'],
        ];
    }
}
