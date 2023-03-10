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
        return [
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
            'contact_list.*' => ['numeric', 'min:1', Rule::exists('contact_lists','uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
            'remind' => ['nullable', 'array', 'min:1'],
            'remind.*' => ['numeric', 'min:1', Rule::exists('reminds','uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
            'contact_company_position' => ['nullable', 'array', 'min:1'],
            'contact_company_position.*.company_uuid' => ['numeric', Rule::exists('companies','uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
            'contact_company_position.*.position_uuid' => ['numeric', Rule::exists('positions','uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
            'user_uuid' => ['numeric', 'min:1', Rule::exists('users','uuid')->whereNull('deleted_at')],
            'status_uuid' => ['numeric', 'min:1', Rule::exists('status','uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
        ];
    }
}
