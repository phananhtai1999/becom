<?php

namespace App\Http\Requests;

use App\Models\Role;
use App\Services\ConfigService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class RemoveBusinessMemberRequest extends FormRequest
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
        $validates = [];
        if ((new ConfigService())->checkUserRoles([Role::ROLE_ROOT, Role::ROLE_ADMIN])) {
            $validates['business_uuid'] = ['required', 'integer', Rule::exists('business_managements', 'uuid')->whereNull('deleted_at')];
        }

        return $validates;
    }
}
