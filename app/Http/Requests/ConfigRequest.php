<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class ConfigRequest extends AbstractRequest
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
            'key' => ['required', 'string', Rule::unique('configs')->whereNull('deleted_at')],
            'value' => ['nullable', 'string'],
            'default_value' => ['nullable', 'string'],
            'group_id' => ['required', 'numeric', 'min:1', Rule::exists('groups', 'uuid')->whereNull('deleted_at')],
        ];
    }
}
