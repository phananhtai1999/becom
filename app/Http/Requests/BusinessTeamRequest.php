<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Role;
use Techup\ApiConfig\Services\ConfigService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BusinessTeamRequest extends AbstractRequest
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
        $validates = [
            'name' => ['required', 'string'],
            'department_uuid' => ['integer', 'exists:departments,uuid'],
            'location_uuid' => ['integer', 'exists:locations,uuid'],
            'leader_uuid' => ['string', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'parent_team_uuid' => ['integer', Rule::exists('teams', 'uuid')->whereNull('deleted_at')],
            'team_member_uuids' => ['array', 'min:1'],
            'team_member_uuids.*' => ['string', 'min:1', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')]
        ];

        if (auth()->hasRole([Role::ROLE_ROOT, Role::ROLE_ADMIN]))
        {
            $validates['business_uuid'] = ['required', 'integer', Rule::exists('business_managements', 'uuid')->whereNull('deleted_at')];
        }

        return $validates;
    }
}
