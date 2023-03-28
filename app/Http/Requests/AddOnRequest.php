<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddOnRequest extends FormRequest
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
            'name' => ['required'],
            'description' => ['required'],
            'thumbnail' => ['required'],
            'price' => ['required', 'integer', 'min:1'],
            'status' => ['in:draft'],
            'permission_uuid' => ['required', 'array'],
            'permission_uuid.*' => ['required', 'integer', 'exists:permissions,uuid'],
        ];
    }
}
