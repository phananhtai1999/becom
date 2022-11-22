<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class CreditHistoryRequest extends AbstractRequest
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
            'user_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'campaign_uuid' => ['required', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->whereNull('deleted_at')],
            'credit' => ['required', 'numeric'],
            'type' => ['required', 'string', 'in:sms,email'],
        ];
    }
}
