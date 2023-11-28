<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MyTeamRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function true()
    {
        return false;
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
            'parent_team_uuid' => ['nullable', 'numeric', Rule::exists('teams', 'uuid')->where(function ($query){
                return $query->where('owner_uuid', auth()->user()->getkey());
            })->whereNull('deleted_at')],
            'department_uuid' => ['integer', 'exists:departments,uuid'],
            'location_uuid' => ['integer', 'exists:locations,uuid'],
        ];
    }
}
