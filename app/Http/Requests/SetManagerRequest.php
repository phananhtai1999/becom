<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\BusinessManagement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetManagerRequest extends AbstractRequest
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
        $validates = [
            'entity' => ['required', Rule::in([BusinessManagement::DEPARTMENT_ENTITY, BusinessManagement::LOCATION_ENTITY])],
            'user_uuid' => ['required', 'numeric', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'entity_uuid' => ['required']
        ];
        if ($this->request->get('entity') == 'department') {
            $validates['entity_uuid'] = array_merge($validates['entity_uuid'], ['exists:departments,uuid']);
        } else {
            $validates['entity_uuid'] = array_merge($validates['entity_uuid'], ['exists:locations,uuid']);
        }

        return $validates;
    }
}
