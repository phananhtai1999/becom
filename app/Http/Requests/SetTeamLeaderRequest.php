<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetTeamLeaderRequest extends AbstractRequest
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
            'team_uuid' => ['required', 'integer', 'exists:teams,uuid'],
            'team_member_uuid' => ['required', 'string', Rule::exists('user_teams', 'user_uuid')
                ->where('team_uuid', $this->request->get('team_uuid'))]
        ];
    }
}
