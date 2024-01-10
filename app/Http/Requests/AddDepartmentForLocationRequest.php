<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddDepartmentForLocationRequest extends AbstractRequest
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
            'department_uuids' => ['required', 'array'],
            'location_uuid' => ['required', 'integer', 'exists:locations,uuid'],
            'department_uuids.*' => ['required', 'integer', 'exists:teams,uuid']
        ];
    }
}
