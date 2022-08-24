<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use Illuminate\Support\Str;
use Carbon\Carbon;

class MailTemplateVariableService extends AbstractService
{
    /**
     * @param $mailTemplate
     * @param $email
     * @param $smtpAccount
     * @param $campaign
     * @return mixed
     */
    public function renderBody($mailTemplate, $email, $smtpAccount, $campaign = null)
    {
        $fromEmail = $smtpAccount->mail_from_address;
        $websiteName = !empty($campaign) ? $campaign->website->name : $email->website->name;
        $websiteDomain = !empty($campaign) ? $campaign->website->domain : $email->website->domain;
        $websiteDescription = !empty($campaign) ? $campaign->website->description : $email->website->description;
        $toEmail = $email->email ?? $email;
        $emailFirstName = $email->first_name ?? '';
        $emailLastName = $email->last_name ?? '';
        $emailAge = $email->age ?? '';
        $emailCountry = $email->country ?? '';
        $emailCity = $email->city ?? '';
        $emailJob = $email->job ?? '';
        $current = Carbon::now('Asia/Ho_Chi_Minh');
        $currentDay = $current->toDateString();
        $currentTime = $current->toTimeString();
        $search = [
            '{{ $website_name }}',
            '{{ $website_domain }}',
            '{{ $website_description }}',
            '{{ $from_email }}',
            '{{ $to_email }}',
            '{{ $email_first_name }}',
            '{{ $email_last_name }}',
            '{{ $email_age }}',
            '{{ $email_country }}',
            '{{ $email_city }}',
            '{{ $email_job }}',
            '{{ $current_day }}',
            '{{ $current_time }}'

        ];
        $replace = [
            $websiteName, $websiteDomain, $websiteDescription,
            $fromEmail, $toEmail, $emailFirstName, $emailLastName, $emailAge, $emailCountry, $emailCity, $emailJob,
            $currentDay, $currentTime
        ];
        if (!empty($campaign)) {
            $campaignFromDate = $campaign->from_date;
            $campaignToDate = $campaign->to_date;
            $campaignTrackingKey = $campaign->tracking_key;
            $campaignNumberEmailPerUser = $campaign->number_email_per_user;
            $campaignNumberEmailPerDate = $campaign->number_email_per_date;
            $search = array_merge($search, [
                '{{ $campaign_from_date }}',
                '{{ $campaign_to_date }}',
                '{{ $campaign_tracking_key }}',
                '{{ $campaign_number_email_per_user }}',
                '{{ $campaign_number_email_per_date }}'
            ]);
            $replace = array_merge($replace, [
                $campaignFromDate, $campaignToDate, $campaignTrackingKey, $campaignNumberEmailPerUser, $campaignNumberEmailPerDate
            ]);
        }
        $mailTemplate->setRenderedBodyAttribute(Str::replace($search, $replace, $mailTemplate->body));
        return $mailTemplate;
    }

}

