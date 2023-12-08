<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MyUpdateTeamRequest extends FormRequest
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
            'parent_team_uuid' => ['nullable', 'numeric', Rule::exists('teams', 'uuid')->where(function ($query){
                return $query->where([
                    ['owner_uuid', auth()->user()],
                    ['app_id', auth()->appId()]
                ]);
            })->whereNull('deleted_at')]
        ];
    }
}
