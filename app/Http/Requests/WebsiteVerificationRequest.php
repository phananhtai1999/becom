<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class WebsiteVerificationRequest extends AbstractRequest
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
            'domain' => ['required', 'string','regex:/^(?:[-A-Za-z0-9]+\.)+[A-Za-z]{2,6}$/', 'exists:send_projects,domain']
        ];
    }
}
