<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdatePartnerRequest extends AbstractRequest
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
            'first_name' => ['string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'last_name' => ['string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'work_email' => ['string', 'email:rfc,dns'],
            'company_name' => ['nullable', 'string'],
            'phone_number' => ['numeric'],
            'partner_category_uuid' => ['numeric', Rule::exists('partner_categories', 'uuid')->whereNull('deleted_at')],
            'publish_status' => ['numeric', 'min:1', 'max:2'],
            'answer' => ['nullable', 'string'],
        ];
    }
}
