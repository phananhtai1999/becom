<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;

class SetTeamAddOnRequest extends AbstractRequest
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
            'app_uuids' => ['required', 'array'],
            'app_uuids.*' => ['required', 'string', 'exists:apps,uuid']
        ];
    }
}
