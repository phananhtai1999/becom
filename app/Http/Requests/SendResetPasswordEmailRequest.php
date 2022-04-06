<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class SendResetPasswordEmailRequest extends AbstractRequest
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
            'email' => ['required', 'max:255', 'exists:users' , 'email'],
        ];
    }
}
