<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

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
            'domain' => ['required', 'string', Rule::unique('websites')->whereNull('deleted_at')],
            'user_uuid' => ['nullable', 'numeric', 'min:1', 'exists:users,uuid'],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'logo' => ['required', 'string'],
        ];
    }
}
