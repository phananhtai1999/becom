<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class MailTemplateRequest extends AbstractRequest
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
            'subject' => ['required', 'string'],
            'body' => ['required', 'string'],
            'website_uuid' => ['nullable', 'numeric', 'min:1', 'exists:websites,uuid'],
            'user_uuid' => ['numeric', 'min:1', 'exists:users,uuid'],
            'design' => ['required', 'string'],
        ];
    }
}
