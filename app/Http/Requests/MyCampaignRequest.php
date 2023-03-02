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
        $sendType = $this->request->get('send_type');
        $validate = [
            'tracking_key' => ['required', 'string'],
            'mail_template_uuid' => ['required', 'numeric', 'min:1', Rule::exists('mail_templates', 'uuid')->where(function ($query) use ($sendType) {
                return $query->where([
                    ['user_uuid', auth()->user()->getKey()],
                    ['type', $sendType],
                    ['publish_status', true]])->where(function ($q) {
                    $q->where('website_uuid', $this->request->get('website_uuid'))
                        ->orWhere('website_uuid', null);
                })->whereNull('deleted_at');
            })],
            'from_date' => ['required', 'date', 'before_or_equal:to_date'],
            'to_date' => ['required', 'date', 'after_or_equal:from_date'],
            'status' => ['required', 'string', 'in:active,banned'],
            'type' => ['required', 'string', 'in:simple,birthday,scenario'],
            'send_type' => ['required', 'string', 'in:sms,email,telegram,viber'],
            'smtp_account_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('smtp_accounts', 'uuid')->where(function ($query) use ($sendType) {
                if ($sendType == 'email') {
                    return $query->where([
                        ['website_uuid', $this->request->get('website_uuid')],
                        ['user_uuid', 'user_uuid', auth()->user()->getKey()],
                        ['mail_mailer', 'smtp'],
                    ])->whereNull('deleted_at');
                } elseif ($sendType == 'sms') {
                    return $query->where([
                        ['website_uuid', $this->request->get('website_uuid')],
                        ['user_uuid', 'user_uuid', auth()->user()->getKey()],
                    ])->whereNull('deleted_at');
                } else {
                    return $query->where([
                        ['website_uuid', $this->request->get('website_uuid')],
                        ['user_uuid', 'user_uuid', auth()->user()->getKey()],
                        ['mail_mailer', $sendType],
                    ])->whereNull('deleted_at');
                }
            })],
            'website_uuid' => ['required', 'numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'reply_to_email' => ['nullable', 'required_if:send_type,email', 'string', 'email:rfc,dns'],
            'reply_name' => ['nullable', 'required_if:send_type,email', 'string'],
            'was_finished' => ['required', 'boolean'],
            'was_stopped_by_owner' => ['required', 'boolean'],
            'contact_list' => ['nullable', 'array', 'min:1'],
            'contact_list.*' => ['numeric', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })]
        ];

        return $validate;
    }
}
