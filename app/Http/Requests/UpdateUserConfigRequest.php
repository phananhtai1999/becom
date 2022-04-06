<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpdateUserConfigRequest extends AbstractRequest
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
            'app_language' => ['string'],
            'user_language' => ['string'],
            'display_name_style' => ['numeric', 'min:1'],
        ];
    }
}
