<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class StatusRequest extends AbstractRequest
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
            'name' => ['required', 'array', 'min:1'],
            'name.en' => ['required', 'string'],
            'name.*' => ['required', 'string'],
            'points' => ['required', 'numeric', 'min:0', Rule::unique('status','points')->where('user_uuid', $this->get('user_uuid', null))],
            'user_uuid' => ['nullable', 'numeric', Rule::exists('users','uuid')->whereNull('deleted_at')],
        ];
    }
}
