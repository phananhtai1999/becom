<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateWebsitePageShortCodeRequest extends FormRequest
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
            'key' => ['string', Rule::unique('website_page_short_codes', 'key')],
            'name' => ['string'],
            'description' => ['string'],
            'parent_uuids' => ['array'],
            'parent_uuids*' => ['integer', 'exists:website_page_short_codes,uuid'],
            'short_code' => ['string'],
        ];
    }
}
