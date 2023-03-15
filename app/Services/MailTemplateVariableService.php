<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\HtmlString;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MailTemplateVariableService extends AbstractService
{
    /**
     * @param $mailTemplate
     * @param $contact
     * @param $smtpAccount
     * @param $campaign
     * @return mixed
     */
    public function renderBody($mailTemplate, $contact, $smtpAccount, $campaign = null)
    {
        $fromEmail = $smtpAccount ? $smtpAccount->mail_from_address : '';
        $websiteName = !empty($campaign) ? $campaign->website->name : '';
        $websiteDomain = !empty($campaign) ? $campaign->website->domain : '';
        $websiteDescription = !empty($campaign) ? $campaign->website->description : '';
        $toEmail = $contact->email ?? $contact;
        $contactFirstName = $contact->first_name ?? '';
        $contactMiddleName = $contact->middle_name ?? '';
        $contactLastName = $contact->last_name ?? '';
        $contactPhone = $contact->phone ?? '';
        $contactSex = $contact->sex ?? '';
        $contactDob = $contact->dob ?? '';
        $contactCountry = $contact->country ?? '';
        $contactCity = $contact->city ?? '';
        $current = Carbon::now('Asia/Ho_Chi_Minh');
        $currentDay = $current->toDateString();
        $currentTime = $current->toTimeString();
//        $search = [
//            '{{ $website_name }}',
//            '{{ $website_domain }}',
//            '{{ $website_description }}',
//            '{{ $from_email }}',
//            '{{ $to_email }}',
//            '{{ $contact_first_name }}',
//            '{{ $contact_middle_name }}',
//            '{{ $contact_last_name }}',
//            '{{ $contact_phone }}',
//            '{{ $contact_sex }}',
//            '{{ $contact_dob }}',
//            '{{ $contact_country }}',
//            '{{ $contact_city }}',
//            '{{ $current_day }}',
//            '{{ $current_time }}'
//
//        ];
        $search = [
            '{ website_name }',
            '{ website_domain }',
            '{ website_description }',
            '{ from_email }',
            '{ to_email }',
            '{ contact_first_name }',
            '{ contact_middle_name }',
            '{ contact_last_name }',
            '{ contact_phone }',
            '{ contact_sex }',
            '{ contact_dob }',
            '{ contact_country }',
            '{ contact_city }',
            '{ current_day }',
            '{ current_time }'

        ];
        $replace = [
            $websiteName, $websiteDomain, $websiteDescription,
            $fromEmail, $toEmail, $contactFirstName, $contactMiddleName, $contactLastName, $contactPhone, $contactSex, $contactDob, $contactCountry, $contactCity,
            $currentDay, $currentTime
        ];
        if (!empty($campaign)) {
            $campaignFromDate = $campaign->from_date;
            $campaignToDate = $campaign->to_date;
            $campaignTrackingKey = $campaign->tracking_key;
//            $search = array_merge($search, [
//                '{{ $campaign_from_date }}',
//                '{{ $campaign_to_date }}',
//                '{{ $campaign_tracking_key }}',
//            ]);
            $search = array_merge($search, [
                '{ campaign_from_date }',
                '{ campaign_to_date }',
                '{ campaign_tracking_key }',
            ]);
            $replace = array_merge($replace, [
                $campaignFromDate, $campaignToDate, $campaignTrackingKey
            ]);
        }
        $mailTemplate->setRenderedBodyAttribute(Str::replace($search, $replace, $mailTemplate->body));
        return $mailTemplate;
    }

    /**
     * @param $mailTemplate
     * @param $mailSendingHistoryUuid
     * @return mixed
     */
    public function injectTrackingImage($mailTemplate, $mailSendingHistoryUuid)
    {
        $tracking_image = '<img width=1 alt="" height=1 src="'.route('mail-open-tracking', $mailSendingHistoryUuid).'" />';

        $linebreak = app(Str::class)->random(32);
        $html = str_replace("\n", $linebreak, $mailTemplate->rendered_body);

        if (preg_match("/^(.*<body[^>]*>)(.*)$/", $html, $matches)) {
            $html = $matches[1].$matches[2].$tracking_image;
        } else {
            $html = $html . $tracking_image;
        }
        $html = str_replace($linebreak, "\n", $html);

        $mailTemplate->setRenderedBodyAttribute($html);

        return $mailTemplate;
    }

    /**
     * @param $footerTemplate
     * @param $mailTemplate
     * @return mixed
     */
    public function insertFooterTemplateInRenderBody($footerTemplate, $mailTemplate)
    {
        $mailTemplate->setRenderedBodyAttribute($mailTemplate->rendered_body.$footerTemplate->template);
        return $mailTemplate;
    }

}

