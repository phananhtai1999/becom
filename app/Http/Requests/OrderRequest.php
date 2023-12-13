<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class OrderRequest extends AbstractRequest
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
            'user_uuid' => ['required', 'numeric', 'min:1', Rule::exists('user_profiles', 'uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'payment_method_uuid' => ['required', 'numeric', 'min:1', Rule::exists('payment_methods', 'uuid')->whereNull('deleted_at')],
            'credit' => ['required', 'numeric'],
            'total_price' => ['required', 'numeric', 'min:1'],
            'status' => ['required', 'numeric', 'min:1'],
            'note' => ['nullable', 'string'],
        ];
    }
}
