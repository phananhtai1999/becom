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
            'domain' => ['nullable', 'string', 'regex:/^(?!(www|http|https)\.)\w+(\.\w+)+$/', Rule::unique('send_projects')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ])
                    ->whereNull('deleted_at');
            })->ignore($this->id, 'uuid')],
            'user_uuid' => ['numeric', 'min:1', Rule::exists('user_profiles', 'uuid')->whereNull('deleted_at')],
            'domain_uuid' => ['nullable', 'numeric', Rule::exists('domains', 'uuid')->where(function ($query) {
                return $query->where([
                    ['owner_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', $this->request->get('user_uuid') ?? auth()->appId()]
                ])
                    ->whereNull('deleted_at');
            })],
            'name' => ['string'],
            'description' => ['string'],
            'logo' => ['string'],
        ];
    }
}
