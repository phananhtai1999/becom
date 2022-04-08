<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class RecoveryPasswordRequest extends AbstractRequest
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
            'token' => ['required', 'max:255'],
            'password' => ['required', 'min:1' , 'max:255', 'confirmed']
        ];
    }
}
