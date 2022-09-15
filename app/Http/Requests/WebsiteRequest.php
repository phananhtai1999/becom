<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;

class WebsiteRequest extends AbstractRequest
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
            'domain' => ['required', 'string', 'unique:websites,domain'],
            'user_uuid' => ['nullable', 'numeric', 'min:1', 'exists:users,uuid'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'logo' => ['required', 'string'],
        ];
    }
}
