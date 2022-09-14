<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class EmailRequest extends AbstractRequest
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
            'email' => ['required', 'string', 'email:rfc,dns', Rule::unique('emails')->whereNull('deleted_at')],
            'age' => ['nullable', 'numeric', 'min:1'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'job' => ['nullable', 'string'],
            'websites' => ['required', 'array', 'min:1'],
            'websites.*' => ['required', 'numeric', 'min:1', 'exists:websites,uuid'],
            'user_uuid' => ['numeric', 'min:1', 'exists:users,uuid'],
        ];
    }
}
