<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateOrderRequest extends AbstractRequest
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
            'user_uuid' => ['numeric', 'min:1', Rule::exists('user_profiles', 'uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'payment_method_uuid' => ['numeric', 'min:1', Rule::exists('payment_methods', 'uuid')->whereNull('deleted_at')],
            'credit' => ['numeric'],
            'total_price' => ['numeric', 'min:1'],
            'status' => ['numeric', 'min:1'],
            'note' => ['string'],
        ];
    }
}
