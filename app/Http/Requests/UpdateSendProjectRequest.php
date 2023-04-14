<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateSendProjectRequest extends AbstractRequest
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
            'domain' => ['nullable', 'string', Rule::unique('send_projects')->ignore($this->id, 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })],
            'user_uuid' => ['numeric', 'min:1', Rule::exists('users', 'uuid')->whereNull('deleted_at')],
            'domain_uuid' => ['nullable', 'numeric', Rule::exists('domains', 'uuid')->where(function ($query) {
                return $query->where('owner_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())
                    ->whereNull('deleted_at');
            })],
            'name' => ['string'],
            'description' => ['string'],
            'logo' => ['string'],
        ];
    }
}
