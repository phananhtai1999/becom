<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class VerifyDomainWebsiteVerificationRequest extends AbstractRequest
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
            'domain_uuid' => ['required','url','exists:send_projects,domain_uuid', 'exists:domains,uuid']
        ];
    }
}
