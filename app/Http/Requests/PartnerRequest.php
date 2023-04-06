<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class PartnerRequest extends AbstractRequest
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
        $validate = [
            'first_name' => ['required', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'last_name' => ['required', 'string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'work_email' => ['required', 'string', 'email:rfc,dns', Rule::unique('partners')->whereNull('deleted_at')],
            'company_name' => ['nullable', 'string'],
            'phone_number' => ['required', 'numeric'],
            'partner_category_uuid' => ['required', 'numeric', Rule::exists('partner_categories', 'uuid')->whereNull('deleted_at')],
            'user_uuid' => ['nullable', 'numeric', Rule::exists('users', 'uuid')->whereNull('deleted_at'), Rule::unique('partners')->whereNull('deleted_at')]
        ];

        if (!$this->request->get('user_uuid')) {
            $validate['work_email'][] = Rule::unique('users', 'email')->where(function ($query) {
                $query->where('email', $this->request->get('work_email'));
            });
        }

        return $validate;
    }
}
