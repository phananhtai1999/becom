<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class IndexRequest extends AbstractRequest
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
            'per_page' => 'integer|min:1|max:100',
            'page' => 'integer|min:1',
            'sorted_by' =>  'string|in:ASC,asc,DESC,desc'
        ];
    }
}
