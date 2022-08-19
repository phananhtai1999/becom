<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class MyEmailRequest extends AbstractRequest
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
            'email' => ['required', 'string', 'email:rfc,dns', 'unique:emails,email'],
            'age' => ['nullable', 'numeric', 'min:1'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'state' => ['nullable', 'string'],
            'job' => ['nullable', 'string'],
            'website_uuid' => ['required', 'numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
        ];
    }
}
