<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\SendProject;
use Illuminate\Validation\Rule;

class SendProjectRequest extends AbstractRequest
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
            'user_uuid' => ['nullable', 'string', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'domain_uuid' => ['nullable', 'numeric', Rule::exists('domains', 'uuid')->where(function ($query) {
                return $query->where([
                    ['owner_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', $this->request->get('user_uuid') ?? auth()->appId()]
                ])
                    ->whereNull('deleted_at');
            })],
            'business_uuid' => ['required', 'numeric', Rule::exists('business_managements', 'uuid')->where(function ($query) {
                return $query->where('owner_uuid', $this->request->get('user_uuid') ?? auth()->userId())
                    ->whereNull('deleted_at');
            })],
            'name' => ['required', 'string'],
            'description' => ['required', 'string'],
            'logo' => ['required', 'string'],
            'parent_uuid' => ['numeric', 'exists:send_projects,uuid'],
            'status' => ['string', Rule::in([SendProject::STATUS_PRIVATE, SendProject::STATUS_PROTECTED, SendProject::STATUS_PUBLIC])]
        ];
    }
}
