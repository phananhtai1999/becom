<?php

namespace App\Http\Requests;

use App\Rules\CheckDepartmentBelongToBusiness;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetAppForDepartmentRequest extends FormRequest
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
            'department_uuids'=> ['required', 'array'],
            'department_uuids.*' => ['required', 'string', 'min:1', 'distinct',Rule::exists('departments', 'uuid'), new CheckDepartmentBelongToBusiness()],
            'app_uuids'=> ['required', 'array'],
            'app_uuids.*' => ['required', 'string', Rule::exists('user_platform_package', 'platform_package_uuid')
                ->where('user_uuid', auth()->userId())->whereNull('deleted_at')],
            'type' => [Rule::in(['assign', 'unsassign'])]
        ];
    }
}
