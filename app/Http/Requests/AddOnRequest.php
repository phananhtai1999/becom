<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class AddOnRequest extends AbstractRequest
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
            'monthly' => ['required', 'integer', 'min:1'],
            'yearly' => ['required', 'integer', 'min:1'],
            'status' => ['in:draft'],
            'platform_package_uuid' => ['exists:apps,uuid'],
            'group_api_uuids' => ['required', 'array'],
            'group_api_uuids.*' => ['required', 'integer', 'exists:group_api_lists,uuid'],
        ];
    }
}
