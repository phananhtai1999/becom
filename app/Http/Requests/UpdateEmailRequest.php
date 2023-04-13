<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateEmailRequest extends AbstractRequest
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
            'email' => ['string', 'email:rfc,dns', 'unique:emails,email,'.$this->id .',uuid,deleted_at,NULL'],
            'age' => ['numeric', 'min:1'],
            'first_name' => ['string'],
            'last_name' => ['string'],
            'country' => ['string'],
            'city' => ['string'],
            'job' => ['string'],
            'send_projects' => ['array', 'min:1'],
            'send_projects.*' => ['numeric', 'min:1', 'exists:send_projects,uuid'],
            'user_uuid' => ['numeric', 'min:1', 'exists:users,uuid'],
        ];
    }
}
