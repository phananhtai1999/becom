<?php

namespace App\Http\Requests\Purpose;

use App\Abstracts\AbstractRequest;

class UpdatePurposeRequest extends AbstractRequest
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
            'title' => ['array', 'min:1'],
            'title.*' => ['string'],
            'publish_status' => ['numeric', 'min:1', 'max:2']
        ];
    }
}
