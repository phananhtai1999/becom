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
            'email' => ['required', 'string', 'email:rfc,dns', Rule::unique('emails')->whereNull('deleted_at')],
            'age' => ['nullable', 'numeric', 'min:1'],
            'first_name' => ['nullable', 'string'],
            'last_name' => ['nullable', 'string'],
            'country' => ['nullable', 'string'],
            'city' => ['nullable', 'string'],
            'job' => ['nullable', 'string'],
            'send_projects' => ['required', 'array', 'min:1'],
            'send_projects.*' => ['required', 'numeric', 'min:1', Rule::exists('send_projects', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ])->whereNull('deleted_at');
            })],
        ];
    }
}
