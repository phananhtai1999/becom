<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class UpsertMyUserDetailRequest extends AbstractRequest
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
            'about' => ['nullable', 'string'],
            'gender' => ['numeric'],
            'date_of_birth' => ['nullable', 'date_format:Y-m-d'],
        ];
    }
}
