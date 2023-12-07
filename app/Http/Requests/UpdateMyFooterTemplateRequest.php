<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateMyFooterTemplateRequest extends AbstractRequest
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
            'title' => ['string'],
            'template' => ['string'],
            'template_json' => ['string'],
            'type' => ['string', 'in:sms,email,telegram,viber'],
            'active_by_uuid' => ['nullable','numeric', "in:".auth()->user()]
        ];
    }
}
