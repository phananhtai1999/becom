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
            'team_uuid'=> ['required', 'integer', 'exists:teams,uuid'],
            'user_uuids'=> ['required', 'array'],
            'user_uuids.*' => ['required', 'string', 'min:1', 'distinct',Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at'), Rule::exists('user_teams', 'user_uuid')
            ->where('team_uuid', $this->request->get('team_uuid'))->whereNull('deleted_at')],
            'add_on_uuids'=> ['required', 'array'],
            'add_on_uuids.*' => ['required', 'numeric', 'distinct', Rule::exists('team_add_on', 'add_on_uuid')
                ->where('team_uuid', $this->request->get('team_uuid'))->whereNull('deleted_at')]
        ];
    }
}
