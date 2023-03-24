<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateMyStatusRequest extends AbstractRequest
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
            'name' => ['array', 'min:1'],
            'name.*' => ['string'],
            'points' => ['numeric', 'min:0'],
        ];
    }
}
