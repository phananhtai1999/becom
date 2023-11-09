<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateBusinessTeamRequest extends FormRequest
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
            'name' => ['string'],
            'leader_uuid' => ['integer', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'parent_team_uuid' => ['integer', Rule::exists('teams', 'uuid')->whereNull('deleted_at')],
            'team_member_uuids' => ['array', 'min:1'],
            'team_member_uuids.*' => ['integer', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
