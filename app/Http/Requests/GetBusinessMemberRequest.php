<?php

namespace App\Http\Requests;

use App\Models\Role;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class GetBusinessMemberRequest extends FormRequest
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
        $validates = [
            'per_page' => ['integer', 'min:1', 'max:100'],
            'page' => ['integer', 'min:1'],
            'sorted_by' =>  ['string', 'in:ASC,asc,DESC,desc'],
            'filter' => ['array'],
            'filter.*' => ['nullable', 'string'],
            'sort' => ['nullable', 'string'],
            'exclude' => ['array'],
            'exclude.*' => ['nullable', 'string'],
            'search' =>  ['nullable', 'string'],
            'search_by' => ['array'],
            'search_by.*' => ['nullable', 'string'],
            'exclude_team_uuid' => ['integer', 'exists:teams,uuid']
        ];
        if (auth()->user()->roles->whereIn('slug', [Role::ROLE_ROOT, Role::ROLE_ADMIN])->count()) {
            $validates['business_uuid'] = ['required', 'integer', Rule::exists('business_managements', 'uuid')->whereNull('deleted_at')];
        }

        return $validates;
    }
}
