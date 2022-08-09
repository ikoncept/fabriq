<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateImageRequest extends FormRequest
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
            'name' => 'required|max:250',
            'caption' => 'nullable|max:255',
            'alt_text' => 'nullable|max:255',
            'x_position' => 'required|max:4|string',
            'y_position' => 'required|max:4|string',
            'custom_crop' => 'required|boolean',
            'tags' => 'array',
        ];
    }
}
