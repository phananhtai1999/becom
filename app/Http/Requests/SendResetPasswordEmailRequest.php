<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

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
            'email' => ['required', 'max:255', Rule::exists('becom_user_profiles','email')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at') , 'email'],
        ];
    }
}
