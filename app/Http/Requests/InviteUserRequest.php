<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class InviteUserRequest extends AbstractRequest
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
            'team_uuid' => ['required', 'exists:teams,uuid'],
            'type' => ['required', Rule::in(['link', 'account'])],
            'email' => ['required', 'string', 'email:rfc,dns',  Rule::unique('user_profiles', 'email')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at'), 'unique:invites,email'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
        ];
    }
}
