<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateAdminUserRequest extends AbstractRequest
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
            'username' => ['nullable', 'string',"regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/", 'unique:users,username,'.$this->id.',uuid,deleted_at,NULL'],
            'email' => ['string', 'email:rfc,dns', 'unique:users,email,'.$this->id.',uuid,deleted_at,NULL'],
            'password' => ['string', 'confirmed'],
            'first_name' => ['nullable', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'last_name' => ['nullable', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'avatar_img' => ['nullable', 'string'],
            'cover_img' => ['nullable', 'string'],
            'can_add_smtp_account' => ['nullable', 'boolean'],
            'roles' => ['array', 'min:1'],
            'roles.*' => ['numeric', 'min:1', Rule::exists('roles', 'uuid')->where(function ($q){
                return $q->where('name', '<>', 'root')->whereNull('deleted_at');
            })],
        ];
    }
}
