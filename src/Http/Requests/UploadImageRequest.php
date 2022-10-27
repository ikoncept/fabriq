<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadImageRequest extends FormRequest
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
            'url' => 'required_without:image',
            'image' => 'required_without:url|image|dimensions:max_width=5000',
        ];
    }

    public function messages()
    {
        return [
            'image.dimensions' => 'Bilden får inte vara bredare än 5000 pixlar',
        ];
    }
}
