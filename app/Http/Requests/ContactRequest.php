<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class ContactRequest extends AbstractRequest
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
            'email' => ['required', 'string'],
            'first_name' => ['required', 'string'],
            'last_name' => ['required', 'string'],
            'middle_name' => ['nullable', 'string'],
            'phone' => ['nullable', 'numeric'],
            'dob' => ['nullable', 'date_format:Y-m-d'],
            'sex' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'avatar' => ['nullable', 'string'],
            'contact_list' => ['nullable', 'array', 'min:1'],
            'contact_list.*' => ['numeric', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
            'remind' => ['nullable', 'array', 'min:1'],
            'remind.*' => ['numeric', 'min:1', Rule::exists('reminds', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey());
            })->whereNull('deleted_at')],
            'contact_company_position' => ['nullable', 'array', 'min:1'],
            'contact_company_position.*.company_uuid' => ['required', 'numeric', Rule::exists('companies', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
            'contact_company_position.*.position_uuid' => ['nullable', 'numeric', Rule::exists('positions', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
            'user_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'status_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('status', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())
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
