<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Models\UserBusiness;
use App\Rules\InviteRule;
use App\Services\ConfigService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

class AddBusinessMemberRequest extends FormRequest
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
            'type' => ['required', Rule::in([UserBusiness::ALREADY_EXISTS_ACCOUNT, UserBusiness::ACCOUNT_INVITE])],
        ];
        if (auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN]))
        {
            $validate['business_uuid'] = ['required', 'integer', Rule::exists('business_managements', 'uuid')->whereNull('deleted_at')];
        }
        if ($this->request->get('type') == UserBusiness::ALREADY_EXISTS_ACCOUNT){
            $validate['user_uuids'] = ['required', 'array', 'min:1'];
            $validate['user_uuids.*'] = ['required', 'integer', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')];
        } elseif ($this->request->get('type') == UserBusiness::ACCOUNT_INVITE) {
            $validate = array_merge($validate, [
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
            ]);
        }

        return $validate;
    }
}
