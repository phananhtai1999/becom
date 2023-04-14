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
            'send_project_uuid' => ['nullable', 'numeric', 'min:1', 'exists:send_projects,uuid'],
            'design' => ['required', 'string'],
            'publish_status' => ['required', 'numeric', 'min:1', 'max:2'],
            'type' => ['required', 'string', 'in:sms,email,telegram,viber'],
            'image' => ['nullable', 'array'],
            'image.*' => ['string'],
        ];
    }
}
