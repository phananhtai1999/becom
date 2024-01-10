<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class WebsitePageShortCodeRequest extends AbstractRequest
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
            'key' => ['required', 'string', Rule::unique('website_page_short_codes', 'key')],
            'name' => ['required', 'string'],
            'description' => ['string'],
            'parent_uuids' => ['required', 'array'],
            'parent_uuids*' => ['required', 'integer', 'exists:website_page_short_codes,uuid'],
            'short_code' => ['required', 'string'],
        ];
    }
}
