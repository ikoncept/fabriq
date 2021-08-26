<?php

namespace Ikoncept\Fabriq\Http\Requests;

use Ikoncept\Fabriq\Models\Notification;
use Illuminate\Foundation\Http\FormRequest;

class ClearNotificationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        if(request()->user()->hasAllRoles(['admin', 'dev'])) {
            return true;
        }
        return (bool) Notification::where('id', $this->route('id'))
            ->where('user_id', request()->user()->id)
            ->first();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'clear' => 'required|boolean'
        ];
    }
}
