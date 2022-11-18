<?php

namespace App\Http\Requests;

use App\Abstracts\AbstractRequest;
use Illuminate\Validation\Rule;

class UpdateMyCampaignRequest extends AbstractRequest
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
            'tracking_key' => ['string'],
            'mail_template_uuid' => ['numeric', 'min:1', Rule::exists('mail_templates', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'from_date' => ['date', 'before_or_equal:to_date'],
            'to_date' => ['date', 'after_or_equal:from_date'],
            'number_email_per_date' => ['numeric', 'min:1', 'lte:number_email_per_user'],
            'number_email_per_user' => ['numeric', 'min:1', 'gte:number_email_per_date'],
            'status' => ['string', 'in:active,banned'],
            'type' => ['string', 'in:simple,birthday,scenario'],
            'send_type' => ['string', 'in:sms,email'],
            'smtp_account_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('smtp_accounts', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'website_uuid' => ['numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'was_finished' => ['boolean'],
            'was_stopped_by_owner' => ['boolean'],
            'contact_list' => ['nullable', 'array', 'min:1'],
            'contact_list.*' => ['numeric', 'min:1',  Rule::exists('contact_lists', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'not_open_mail_campaign' => ['nullable', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($query) {
                $query->where([
                    ['type', 'scenario'],
                    ['user_uuid', auth()->user()->getkey()]
                ])->whereNull('deleted_at');
            })],
            'open_mail_campaign' =>  ['nullable', 'numeric', 'min:1', Rule::exists('campaigns', 'uuid')->where(function ($query) {
                $query->where([
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
