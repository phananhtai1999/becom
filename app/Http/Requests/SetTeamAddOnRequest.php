<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SetTeamAddOnRequest extends FormRequest
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
            'team_uuid' => ['required', 'exists:teams,uuid'],
            'add_on_uuids' => ['required', 'array'],
            'add_on_uuids.*' => ['required', 'integer', 'exists:add_ons,uuid']
        ];
    }
}
