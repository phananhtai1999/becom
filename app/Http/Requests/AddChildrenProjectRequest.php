<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddChildrenProjectRequest extends AbstractRequest
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
            'send_project_uuid' => ['required', 'integer', 'exists:send_projects,uuid'],
            'children_send_project_uuids' => ['required', 'array'],
            'children_send_project_uuids.*' => ['required', 'integer', 'exists:send_projects,uuid']
        ];
    }
}
