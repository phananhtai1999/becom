<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBankInformationRequest extends AbstractRequest
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
            'swift_code' => ['string', Rule::unique('bank_informations', 'swift_code')->where(function ($q) {
                return $q->where('uuid','!=', $this->id)
                    ->whereNull('deleted_at');
            })],
            'is_verified' => ['boolean'],
            'bank_name' => ['string'],
            'bank_address' => ['string'],
            'currency' => ['string']
        ];
    }
}
