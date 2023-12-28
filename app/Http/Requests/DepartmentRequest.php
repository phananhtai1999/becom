<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class DepartmentRequest extends AbstractRequest
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
            'name' => ['required', 'array', 'min:1'],
            'name.en' => ['required', 'string'],
            'name.*' => ['required', 'string'],
            'user_uuid' => ['nullable', 'numeric', Rule::exists('users','uuid')->whereNull('deleted_at')],
            'business_uuid' => ['integer', 'exists:business_managements,uuid'],
        ];
    }
}
