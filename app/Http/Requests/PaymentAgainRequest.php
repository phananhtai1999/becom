<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Order;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PaymentAgainRequest extends AbstractRequest
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
            'order_id' => [
                'required',
                'numeric',
                Rule::exists('orders', 'uuid')->where(function ($query) {

                    return $query->where([
                        ['status', Order::ORDER_PENDING_REQUEST_STATUS],
                        ['user_uuid', auth()->userId()],
                        ['app_id', auth()->appId()],
                    ])
                        ->whereNull('deleted_at');
                })
            ]
        ];
    }
}
