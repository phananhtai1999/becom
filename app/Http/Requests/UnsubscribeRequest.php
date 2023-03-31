<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UnsubscribeRequest extends AbstractRequest
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
            'code' => ['required', 'exists:unsubscribes,code'],
            'business_categories' => ['nullable', 'array'],
            'business_categories.*' => ['required', 'numeric', Rule::exists('business_categories', 'uuid')->where(function ($query) {
                $query->whereNull('parent_uuid')->whereNull('deleted_at');
            })],
            'unsubscribes' => ['nullable', 'array'],
            'unsubscribes.*' => ['required', 'string', 'in:email,sms,telegram,viber', 'distinct'],
        ];
    }
}
