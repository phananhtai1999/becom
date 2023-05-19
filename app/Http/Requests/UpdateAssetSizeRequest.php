<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAssetSizeRequest extends FormRequest
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
            'name' => ['string'],
            'width' => ['integer', 'min:1'],
            'height' => ['integer', 'min:1'],
            'asset_group_code' => ['string', 'exists:asset_groups,code']
        ];
    }
}
