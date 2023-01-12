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
                return $query->where('user_uuid', auth()->user()->getKey())->where(function ($q) {
                    $q->where('website_uuid', $this->request->get('website_uuid'))
                        ->orWhere('website_uuid', null);
                })->where('publish_status', true)->whereNull('deleted_at');
            })],
            'from_date' => ['date', 'before_or_equal:to_date'],
            'to_date' => ['date', 'after_or_equal:from_date'],
            'status' => ['string', 'in:active,banned'],
            'type' => ['string', 'in:simple,birthday,scenario'],
            'send_type' => ['string', 'in:sms,email'],
            'smtp_account_uuid' => ['nullable', 'numeric', 'min:1', Rule::exists('smtp_accounts', 'uuid')->where(function ($query) {
                return $query->where([
                    ['website_uuid', $this->request->get('website_uuid')],
                    ['user_uuid', auth()->user()->getKey()]
                ])->whereNull('deleted_at');
            })],
            'website_uuid' => ['numeric', 'min:1', Rule::exists('websites', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })],
            'was_finished' => ['boolean'],
            'was_stopped_by_owner' => ['boolean'],
            'contact_list' => ['nullable', 'array', 'min:1'],
            'contact_list.*' => ['numeric', 'min:1',  Rule::exists('contact_lists', 'uuid')->where(function ($query) {

                return $query->where('user_uuid', auth()->user()->getkey())->whereNull('deleted_at');
            })]
        ];

        return $validate;
    }
}
