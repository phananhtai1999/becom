<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Rules\ResetPasswordTeamMemberRule;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class ResetPasswordEmailTeamMemberRequest extends AbstractRequest
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
        $validate = [
            'user_uuid' => ['required', 'numeric', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at'), Rule::exists('user_teams', 'user_uuid')->whereNull('deleted_at')],
            'password' => ['required', 'string', 'regex:/^\S*$/',
                Password::min(8)
                    ->letters()
                    ->mixedCase()
                    ->numbers()
                    ->symbols(),
                'max:255'
            ],
            'password_confirmation' => ['required', 'string', 'same:password']
        ];

        if($this->user()->roles->whereNotIn('slug', ["admin", "root"])->count()){
            $validate['user_uuid'] = array_merge($validate['user_uuid'], [new ResetPasswordTeamMemberRule($this->request->get('user_uuid'))]);
        }

        return $validate;
    }
}
