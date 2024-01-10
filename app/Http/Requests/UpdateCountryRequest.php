<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends AbstractRequest
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
            'sms_price' => ['integer', 'min:1'],
            'email_price' => ['integer', 'min:1'],
            'telegram_price' => ['integer', 'min:1'],
            'viber_price' => ['integer', 'min:1'],
        ];
    }
}
