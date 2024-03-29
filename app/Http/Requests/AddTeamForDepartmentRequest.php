<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddTeamForDepartmentRequest extends AbstractRequest
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
            'team_uuids' => ['required', 'array'],
            'department_uuid' => ['required', 'integer', 'exists:departments,uuid'],
            'team_uuids.*' => ['required', 'integer', 'exists:teams,uuid']
        ];
    }
}
