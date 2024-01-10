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
            'user_uuid' => ['nullable', 'string', Rule::exists('becom_user_profiles','user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'location_uuid' => ['integer', 'exists:locations,uuid'],
        ];
    }
}
