<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\SendProject;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignProjectForDepartmentRequest extends AbstractRequest
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
            'send_project_uuid'  => ['required', 'numeric', Rule::exists('send_projects', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->userId())
                    ->whereNull('deleted_at');
            })],
            'department_uuids' => ['required', 'array'],
            'department_uuids.*' => ['required', 'integer', 'exists:departments,uuid'],
            'status' => ['string', Rule::in([SendProject::STATUS_PRIVATE, SendProject::STATUS_PROTECTED])]
        ];
    }
}
