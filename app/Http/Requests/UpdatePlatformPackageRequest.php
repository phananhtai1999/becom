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
            'name' => ['unique:apps,uuid'],
            'monthly' => ['min:-1', 'integer'],
            'yearly' => ['min:-1', 'integer'],
            'group_api_uuids' => ['array'],
            'group_api_uuids.*' => ['integer', 'exists:group_api_lists,uuid'],
        ];

    }
}
