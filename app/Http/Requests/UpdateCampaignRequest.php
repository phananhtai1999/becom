<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use App\Models\Campaign;
use App\Models\ContactList;
use App\Services\UserTeamService;
use Illuminate\Validation\Rule;

class UpdateCampaignRequest extends AbstractRequest
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
            'tracking_key' => ['string'],
            'mail_template_uuid' => ['integer', 'min:1', Rule::exists('mail_templates', 'uuid')->where(function ($query) use ($sendType) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()],
                    ['type', $sendType],
                    ['publish_status', true]])->where(function ($q) {
                    $q->where('send_project_uuid', $this->request->get('send_project_uuid'))
                        ->orWhere('send_project_uuid', null);
                })->whereNull('deleted_at');
            })],
            'from_date' => ['date', 'before_or_equal:to_date'],
            'to_date' => ['date', 'after_or_equal:from_date'],
            'status' => ['string', 'in:active,banned'],
            'type' => ['string', 'in:simple,birthday,scenario'],
            'send_type' => ['string', 'in:sms,email,telegram,viber'],
            'smtp_account_uuid' => ['nullable', 'integer', 'min:1', Rule::exists('smtp_accounts', 'uuid')->where(function ($query) use ($sendType) {
                if ($sendType == Campaign::CAMPAIGN_EMAIL_SEND_TYPE) {
                    return $query->where([
                        ['send_project_uuid', $this->request->get('send_project_uuid')],
                        ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                        ['app_id', auth()->appId()],
                        ['mail_mailer', 'smtp'],
                        ['status', 'work'],
                        ['publish', true],
                    ])->whereNull('deleted_at');
                } elseif ($sendType == Campaign::CAMPAIGN_SMS_SEND_TYPE) {
                    return $query->where([
                        ['send_project_uuid', $this->request->get('send_project_uuid')],
                        ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                        ['app_id', auth()->appId()],
                        ['status', 'work'],
                        ['publish', true],
                    ])->whereNull('deleted_at');
                } else {
                    return $query->where([
                        ['send_project_uuid', $this->request->get('send_project_uuid')],
                        ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                        ['app_id', auth()->appId()],
                        ['mail_mailer', $sendType],
                        ['status', 'work'],
                        ['publish', true],
                    ])->whereNull('deleted_at');
                }
            })],
            'send_project_uuid' => ['integer', 'min:1', Rule::exists('send_projects', 'uuid')->where(function ($query) {
                return $query->where([
                    ['user_uuid', $this->request->get('user_uuid') ?? auth()->userId()],
                    ['app_id', auth()->appId()]
                ])->whereNull('deleted_at');
            })],
            'reply_to_email' => ['nullable', 'required_if:send_type,email', 'string', 'email:rfc,dns'],
            'reply_name' => ['nullable', 'required_if:send_type,email', 'string'],
            'send_from_email' => ['nullable', 'string', 'email:rfc,dns'],
            'send_from_name' => ['nullable', 'string'],
            'was_finished' => ['boolean'],
            'was_stopped_by_owner' => ['boolean'],
            'user_uuid' => ['nullable', 'string', 'min:1', Rule::exists('becom_user_profiles', 'user_uuid')->where(function ($q) {
                return $q->where('app_id', auth()->appId());
            })->whereNull('deleted_at')],
            'contact_list' => ['array', 'min:1'],
            'contact_list.*' => ['integer', 'min:1', Rule::exists('contact_lists', 'uuid')->where(function ($query) {
                //Check team
                $userUuid = $this->request->get('user_uuid') ?? auth()->userId();
                $userTeam = (new UserTeamService())->getUserTeamByUserAndAppId($userUuid, auth()->appId());
                if ($userTeam) {
                    $userUuid = $userTeam->team->owner_uuid;
                }
                return $query->where([
                    ['user_uuid', $userUuid],
                    ['app_id', auth()->appId()]
                ])->whereNull('deleted_at');
            }),
                //Check contact phone
                function ($attribute, $value, $fail) use ($sendType) {
                if ($sendType === Campaign::CAMPAIGN_SMS_SEND_TYPE ||
                    Campaign::CAMPAIGN_TELEGRAM_SEND_TYPE ||
                    Campaign::CAMPAIGN_VIBER_SEND_TYPE
                ) {
                    $contactList = ContactList::find($value);
                    if ($contactList && $contactList->contacts()->whereNull('phone')->exists()) {
                        $fail(__('messages.contact_must_have_phone'));
                    }
                }
            }]
        ];

        return $validate;
    }
}
