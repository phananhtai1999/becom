<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ConfirmWithdrawalRequest extends AbstractRequest
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
            'status' => ['required', 'in:reject,accept'],
            'partner_payouts'=> ['required', 'array'],
            'partner_payouts.*' => ['required', 'numeric', Rule::exists('partner_payouts', 'uuid')->where(function ($query) {
                $query->where('status', 'new');
            })]
        ];
    }
}
