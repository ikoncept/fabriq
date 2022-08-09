<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateEventRequest extends FormRequest
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
            'date' => 'required|array',
            'start_time' => 'nullable|max:5',
            'end_time' => 'nullable|max:5',
            'full_day' => 'boolean',
            'localizedContent' => 'required|array',
            'daily_interval' => 'nullable',
            // 'localizedContent.*.title' => 'required'
        ];
    }
}
