<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class UpgradeUserRequest extends AbstractRequest
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
            'payment_method_uuid' => ['required', Rule::in('1', '2')],
            'go_back_url' => ['required'],
            'billing_address_uuid' => ['required', 'exists:billing_addresses,uuid'],
            "subscription_plan_uuid" => ['required', 'integer', 'exists:subscription_plans,uuid']
        ];
    }
}
