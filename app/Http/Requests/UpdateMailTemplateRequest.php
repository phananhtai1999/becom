<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateMailTemplateRequest extends AbstractRequest
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
            'website_uuid' => ['numeric', 'min:1', 'exists:websites,uuid'],
            'design' => ['string']
        ];
    }
}
