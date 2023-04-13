<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateUnpublishedMailTemplateRequest extends AbstractRequest
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
            'subject' => ['string'],
            'body' => ['string'],
            'send_project_uuid' => ['nullable', 'numeric', 'min:1', 'exists:send_projects,uuid'],
            'design' => ['string'],
            'type' => ['string', 'in:sms,email,telegram,viber'],
        ];
    }
}
