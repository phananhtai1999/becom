<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Rules\CheckTeamBeLongToBusinessRule;
use App\Rules\TeamsBelongToSameBusinessRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class AssignProjectForTeamRequest extends AbstractRequest
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
            'send_project_uuid'  => ['required', 'numeric', Rule::exists('send_projects', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->userId())
                    ->whereNull('deleted_at');
            })],
            'team_uuids' => ['required', 'array'],
            'team_uuids.*' => ['required', 'integer', 'exists:teams,uuid', new CheckTeamBeLongToBusinessRule()]
        ];
    }
}
