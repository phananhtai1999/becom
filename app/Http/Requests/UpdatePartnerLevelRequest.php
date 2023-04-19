<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdatePartnerLevelRequest extends AbstractRequest
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
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'number_of_customers' => ['numeric', 'min:0', 'unique:partner_levels,number_of_customers,'.$this->id .',uuid'],
            'commission' => ['numeric', 'min:0'],
            'content' => ['array', 'min:1'],
            'content.*' => ['array', 'min:1'],
            'content.*.en' => ['string'],
            'content.*.*' => ['string'],
        ];
    }
}
