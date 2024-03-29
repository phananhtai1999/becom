<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class PlatformPackageRequest extends AbstractRequest
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
            'name' => ['required', 'unique:apps,name'],
            'parent_uuid' => ['exists:apps,uuid'],
            'service' => ['required'],
            'group_api_codes' => ['required', 'array'],
            'description' => ['required'],
            'avatar' => ['string'],
            'monthly' => ['required', 'min:-1', 'integer'],
            'yearly' => ['required', 'min:-1', 'integer']
        ];
    }
}
