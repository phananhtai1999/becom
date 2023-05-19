<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetRequest extends FormRequest
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
            'url' => ['string'],
            'title' => ['string'],
            'asset_group_code' => ['string', 'exists:asset_groups,code'],
            'asset_size_uuid' => ['integer', 'exists:asset_sizes,uuid'],
            'type' => ['string'],
        ];
    }
}
