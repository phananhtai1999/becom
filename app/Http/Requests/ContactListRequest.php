<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class ContactListRequest extends AbstractRequest
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
            'name' => ['required', 'string'],
            'contact' => ['nullable', 'array', 'min:1'],
            'contact.*' => ['numeric', 'min:1', 'exists:contacts,uuid'],
            'user_uuid' => ['required', 'numeric', 'min:1', 'exists:users,uuid'],
        ];
    }
}
