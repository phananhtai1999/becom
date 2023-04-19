<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class PartnerLevelRequest extends AbstractRequest
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
            'image' => ['nullable', 'string'],
            'title' => ['required', 'array', 'min:1'],
            'title.en' => ['required', 'string'],
            'title.*' => ['required', 'string'],
            'number_of_customers' => ['required', 'numeric', 'min:0', 'unique:partner_levels,number_of_customers'],
            'commission' => ['required', 'numeric', 'min:0'],
            'content' => ['required', 'array', 'min:1'],
            'content.*' => ['required', 'array', 'min:1'],
            'content.*.en' => ['required', 'string'],
            'content.*.*' => ['required', 'string'],
        ];
    }
}
