<?php

namespace App\Http\Requests;

use App\Rules\CheckLocationOwnerRule;
use Illuminate\Foundation\Http\FormRequest;

class RemoveTeamFromLocationRequest extends FormRequest
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
            'team_uuids' => ['required', 'array'],
            'location_uuid' => ['required', 'integer', 'exists:locations,uuid', new CheckLocationOwnerRule()],
            'team_uuids.*' => ['required', 'integer', 'exists:teams,uuid']
        ];
    }
}
