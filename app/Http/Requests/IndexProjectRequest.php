<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class IndexProjectRequest extends AbstractRequest
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
            'per_page' => ['integer', 'min:1', 'max:200'],
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
            'type' => ['array'],
            'type.departments.*' => ['exists:departments,uuid'],
            'type.locations.*' => ['exists:locations,uuid'],
            'type.teams.*' => ['exists:teams,uuid'],
        ];
    }
}
