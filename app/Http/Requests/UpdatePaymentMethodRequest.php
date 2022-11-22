<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdatePaymentMethodRequest extends AbstractRequest
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
            'name' => ['string', Rule::unique('payment_methods')->whereNull('deleted_at')],
            'slug' => ['string', Rule::unique('payment_methods')->whereNull('deleted_at')],
        ];
    }
}
