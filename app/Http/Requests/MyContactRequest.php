<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class MyContactRequest extends AbstractRequest
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
            'contact_list.*' => ['integer', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {

                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])->whereNull('deleted_at');
            })],
            'remind' => ['nullable', 'array', 'min:1'],
            'remind.*' => ['integer', 'min:1', Rule::exists('reminds', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')],
            'company_uuid' => ['required_with:positions', 'integer', Rule::exists('companies', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
            'position_ids' => ['array'],
            'position_ids.*' => ['integer', 'min:1', Rule::exists('positions', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
            'department_uuid' => ['nullable', 'integer', 'min:1', Rule::exists('departments', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')],
            'status_uuid' => ['nullable', 'integer', 'min:1', Rule::exists('status', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->orWhereNull('user_uuid');
            })->whereNull('deleted_at')],
        ];
    }
}
