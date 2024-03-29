<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateTeamRequest extends AbstractRequest
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
            'parent_team_uuid' => ['nullable', 'numeric', Rule::exists('teams', 'uuid')->whereNull('deleted_at')]
        ];
    }
}
