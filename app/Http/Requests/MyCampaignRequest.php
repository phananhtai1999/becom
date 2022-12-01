<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class MyCampaignRequest extends AbstractRequest
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
                    ['website_uuid', $this->request->get('website_uuid')],
                    ['user_uuid', auth()->user()->getKey()]
                ])->whereNull('deleted_at');
            })],
            'from_date' => ['required', 'date', 'before_or_equal:to_date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'number_email_per_date' => ['required', 'numeric', 'min:1', 'lte:number_email_per_user'],
            'number_email_per_user' => ['required', 'numeric', 'min:1', 'gte:number_email_per_date'],
            'status' => ['required', 'string', 'in:active,banned'],
            'type' => ['required', 'string', 'in:simple,birthday,scenario'],
            'send_type' => ['required', 'string', 'in:sms,email'],
            'smtp_account_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('smtp_accounts', 'uuid')->where(function ($query) {
                return $query->where([
                    ['website_uuid', $this->request->get('website_uuid')],
                    ['user_uuid', auth()->user()->getKey()]
                ])->whereNull('deleted_at');
            })],
            'website_uuid' => ['required', 'numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'was_finished' => ['required', 'boolean'],
            'was_stopped_by_owner' => ['required', 'boolean'],
            'contact_list' => ['nullable', 'array', 'min:1'],
            'contact_list.*' => ['numeric', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'not_open_mail_campaign' => ['nullable', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($query) {
                return $query->where([
                    ['type', 'scenario'],
                    ['user_uuid', auth()->user()->getkey()]
                ])->whereNull('deleted_at');
            })],
            'open_mail_campaign' =>  ['nullable', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($query) {
                return $query->where([
                    ['type', 'scenario'],
                    ['user_uuid', auth()->user()->getkey()]
                ])->whereNull('deleted_at');
            })],
            'open_within' => ['nullable', 'numeric', 'min:1']
        ];

        if (!empty($this->request->get('not_open_mail_campaign'))){
            unset($validate['open_within'][0]);
            array_unshift($validate['open_within'], "required");
        }

        return $validate;
    }
}
