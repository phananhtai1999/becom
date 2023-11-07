<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessTeamRequest extends FormRequest
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
            'name' => ['required', 'string'],
            'leader_uuid' => ['required', 'integer', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'team_uuid' => ['required_without_all:team_member_uuid.*', 'integer', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'business_uuid' => ['required', 'integer', Rule::exists('business_managements', 'uuid')->whereNull('deleted_at')],
            'team_member_uuids' => ['required_without_all:team_uuid', 'array', 'min:1'],
            'team_member_uuid.*' => ['required_without_all:team_uuid', 'integer', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
