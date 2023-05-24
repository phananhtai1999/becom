<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AssetRequest extends FormRequest
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
            'file' => ['required', 'mimes:jpg,png,gif', 'max:153600'],
            'title' => ['required', 'string'],
            'asset_group_code' => ['required', 'string', 'exists:asset_groups,code'],
            'asset_size_uuid' => ['required', 'integer', 'exists:asset_sizes,uuid'],
            'type' => ['required', 'string'],
        ];
    }
}
