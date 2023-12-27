<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Role;
use Illuminate\Validation\Rule;

class MySendProjectRequest extends AbstractRequest
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
            'domain' => ['nullable', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/', Rule::unique('send_projects')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'logo' => ['required', 'string'],
            'domain_uuid' => ['nullable', 'numeric', Rule::exists('domains', 'uuid')->where(function ($query) {
                return $query->where('owner_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })],
            'parent_uuid' => ['numeric', 'exists:send_projects,uuid'],
        ];

    }
}
