<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class CampaignRequest extends AbstractRequest
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
        $validate = [
            'tracking_key' => ['required', 'string'],
            'mail_template_uuid' => ['required', 'numeric', 'min:1', Rule::exists('mail_templates', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey()],
                    ['type', $this->request->get('send_type')],
                    ['publish_status', true]])->where(function ($q) {
                    $q->where('website_uuid', $this->request->get('website_uuid'))
                        ->orWhere('website_uuid', null);
                })->whereNull('deleted_at');
            })],
            'from_date' => ['required', 'date', 'before_or_equal:to_date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'status' => ['required', 'string', 'in:active,banned'],
            'type' => ['required', 'string', 'in:simple,birthday,scenario'],
            'send_type' => ['required', 'string', 'in:sms,email'],
            'smtp_account_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('smtp_accounts', 'uuid')->where(function ($query) {
                return $query->where([
                    ['website_uuid', $this->request->get('website_uuid')],
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey()]
                ])->whereNull('deleted_at');
            })],
            'website_uuid' => ['required', 'numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())->whereNull('deleted_at');
            })],
            'was_finished' => ['required', 'boolean'],
            'was_stopped_by_owner' => ['required', 'boolean'],
            'user_uuid' => ['nullable', 'numeric', 'min:1', 'exists:users,uuid'],
            'contact_list' => ['required', 'array', 'min:1'],
            'contact_list.*' => ['required', 'numeric', 'min:1',  Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())->whereNull('deleted_at');
            })]
        ];
        return $validate;

    }
}
