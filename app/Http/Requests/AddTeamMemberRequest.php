<?php

namespace App\Http\Requests;

use App\Rules\InviteRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AddTeamMemberRequest extends FormRequest
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
            'team_uuid' => ['required', Rule::exists('teams', 'uuid')->whereNull('deleted_at')],
            'type' => ['required', Rule::in(['link', 'account'])],
            'username' => ['required', 'string', "regex:/^(?!.*\.\.)[a-zA-Z0-9]*(?:\.[a-zA-Z0-9]+)*$/", Rule::unique('users', 'username')->whereNull('deleted_at'), new InviteRule($this->request->get('domain'))],
            'first_name' => ['required', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'last_name' => ['required', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'domain' => ['required', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/', Rule::exists('domains', 'name')->where(function ($query) {
                return $query->where([
                    ['verified_at', '<>', null],
                    ['active_mailbox', true]
                ])->whereNull('deleted_at');
            })],
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
    }
}
