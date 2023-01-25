<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateBlockTypeRequest extends FormRequest
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
            'component_name' => ['required', 'max:255', 'unique:block_types,component_name'],
            'has_children' => ['boolean', 'required'],
        ];
    }
}
