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
        $sendType = $this->request->get('send_type');
        $validate = [
            'tracking_key' => ['required', 'string'],
            'mail_template_uuid' => ['required', 'numeric', 'min:1', Rule::exists('mail_templates', 'uuid')->where(function ($query) use ($sendType) {

                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey()],
                    ['type', $sendType],
                    ['publish_status', true]])->where(function ($q) {
                    $q->where('send_project_uuid', $this->request->get('send_project_uuid'))
                        ->orWhere('send_project_uuid', null);
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
                        ['send_project_uuid', $this->request->get('send_project_uuid')],
                        ['user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey()],
                        ['mail_mailer', 'smtp'],
                        ['status', 'work'],
                        ['publish', true],
                    ])->whereNull('deleted_at');
                } elseif ($sendType == 'sms') {
                    return $query->where([
                        ['send_project_uuid', $this->request->get('send_project_uuid')],
                        ['user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey()],
                        ['status', 'work'],
                        ['publish', true],
                    ])->whereNull('deleted_at');
                } else {
                    return $query->where([
                        ['send_project_uuid', $this->request->get('send_project_uuid')],
                        ['user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey()],
                        ['mail_mailer', $sendType],
                        ['status', 'work'],
                        ['publish', true],
                    ])->whereNull('deleted_at');
                }
            })],
            'send_project_uuid' => ['required', 'numeric', 'min:1', Rule::exists('send_projects', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())->whereNull('deleted_at');
            })],
            'was_finished' => ['required', 'boolean'],
            'was_stopped_by_owner' => ['required', 'boolean'],
            'user_uuid' => ['nullable', 'numeric', 'min:1', 'exists:users,uuid'],
            'reply_to_email' => ['nullable', 'required_if:send_type,email', 'string', 'email:rfc,dns'],
            'reply_name' => ['nullable', 'required_if:send_type,email', 'string'],
            'send_from_email' => ['nullable', 'string', 'email:rfc,dns'],
            'send_from_name' => ['nullable', 'string'],
            'contact_list' => ['required', 'array', 'min:1'],
            'contact_list.*' => ['required', 'numeric', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                return $query->where('user_uuid', $this->request->get('user_uuid') ?? auth()->user()->getKey())->whereNull('deleted_at');
            })]
        ];
        return $validate;

    }
}
