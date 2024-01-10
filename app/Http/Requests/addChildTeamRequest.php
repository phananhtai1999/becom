<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Rules\checkTeamOwnerRule;
use App\Rules\TeamsBelongToSameBusinessRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class addChildTeamRequest extends AbstractRequest
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
            'team_uuid' => ['required', 'integer', 'exists:teams,uuid', new checkTeamOwnerRule()],
            'child_team_uuids' => ['required', 'array'],
            'child_team_uuids.*' => ['required', 'integer', 'exists:teams,uuid', new checkTeamOwnerRule(), new TeamsBelongToSameBusinessRule($this->request->get('team_uuid'))]
        ];
    }
}
