<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SetAddOnForMemberRequest extends AbstractRequest
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
            'user_uuid' => ['required', 'integer', 'min:1', 'exists:users,uuid', Rule::exists('user_teams', 'user_uuid')
            ->where('team_uuid', $this->id)->whereNull('deleted_at')],
            'add_on_uuids'=> ['required', 'array', 'distinct'],
            'add_on_uuids.*' => ['required', 'numeric', Rule::exists('team_add_on', 'add_on_uuid')
                ->where('team_uuid', $this->id)->whereNull('deleted_at')]
        ];
    }
}
