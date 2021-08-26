<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateArticleRequest extends FormRequest
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
            'name' => 'required|max:255',
            'publishes_at' => 'date|nullable',
            'unpublishes_at' => 'date|nullable',
            'has_unpublished_time' => 'boolean',
            'content.article_title' => 'required|max:255',
            'content' => 'array'
        ];
    }
}
