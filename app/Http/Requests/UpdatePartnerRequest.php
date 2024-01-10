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
        $validate = [
            'first_name' => ['string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'last_name' => ['string', "regex:/^[^(\|\]~`!@#$%^&*+=\-_{}\\\;:\"'?><,.\/’)\[]*$/"],
            'partner_email' => ['string', Rule::unique('partners')->ignore($this->id, 'uuid')->whereNull('deleted_at')],
            'company_name' => ['nullable', 'string'],
            'phone_number' => ['numeric'],
            'partner_category_uuid' => ['numeric', Rule::exists('partner_categories', 'uuid')->whereNull('deleted_at')],
            'answer' => ['nullable', 'string'],
            'user_uuid' => ['nullable', 'numeric', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at'), Rule::unique('partners')->ignore($this->id, 'uuid')->whereNull('deleted_at')]
        ];

        if (!$this->request->get('user_uuid')) {
            $validate['partner_email'][] = 'email:rfc,dns';
            $validate['partner_email'][] = Rule::unique('becom_user_profiles', 'email')->where(function ($query) {
                $query->where([
                    ['email', $this->request->get('partner_email')],
                    ['app_id', auth()->appId()]
                ]);
            });
        } else {
            $validate['partner_email'][] = Rule::exists('becom_user_profiles', 'email')->where(function ($query) {
                $query->where([
                    ['email', $this->request->get('partner_email')],
                    ['app_id', auth()->appId()],
                    ['uuid', $this->request->get('user_uuid')]
                ]);
            });
        }

        return $validate;
    }
}
