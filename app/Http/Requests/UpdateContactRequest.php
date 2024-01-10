<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateContactRequest extends AbstractRequest
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
            'email' => ['string'],
            'first_name' => ['string'],
            'last_name' => ['string'],
            'middle_name' => ['nullable', 'string'],
            'phone' => ['nullable', 'numeric'],
            'dob' => ['nullable', 'date_format:Y-m-d'],
            'sex' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string'],
            'contact_list' => ['nullable', 'array', 'min:1'],
            'contact_list.*' => ['numeric', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')],
            'remind' => ['nullable', 'array', 'min:1'],
            'remind.*' => ['numeric', 'min:1', Rule::exists('reminds', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')],
            'contact_company_position' => ['nullable', 'array', 'min:1'],
            'contact_company_position.*.company_uuid' => ['numeric', Rule::exists('companies', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
            'contact_company_position.*.position_uuid' => ['nullable', 'numeric', Rule::exists('positions', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
            'contact_company_position.*.department_uuid' => ['nullable', 'numeric', Rule::exists('departments', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
            'user_uuid' => ['string', 'min:1', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'status_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('status', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
        ];

        if (is_array($this->request->get('contact_company_position'))) {
            foreach ($this->request->get('contact_company_position') as $key => $value) {
                if (!is_integer($key)) {
                    $validate['contact_company_position.*'] = ['numeric', 'min:0'];
                }
            }
        }

        return $validate;
    }
}
