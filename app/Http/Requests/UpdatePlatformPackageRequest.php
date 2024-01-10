<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePlatformPackageRequest extends AbstractRequest
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
            'name' => ['unique:platform_packages,uuid'],
            'monthly' => ['min:-1', 'integer'],
            'yearly' => ['min:-1', 'integer'],
            'permission_uuid' => ['array'],
            'permission_uuid.*' => ['integer', 'exists:permissions,uuid'],
        ];

    }
}
