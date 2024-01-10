<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class TeamRequest extends AbstractRequest
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
            'owner_uuid' => ['required', Rule::exists('user_profiles', 'uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'parent_team_uuid' => ['nullable', 'numeric', Rule::exists('teams', 'uuid')->whereNull('deleted_at')],
            'department_uuid' => ['integer', 'exists:departments,uuid'],
            'location_uuid' => ['integer', 'exists:locations,uuid']
        ];
    }
}
