<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class ChangePasswordRequest extends AbstractRequest
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
            'old_password' => ['required', 'string'],
            'new_password' => ['required', 'string','confirmed'],
        ];
    }
}
