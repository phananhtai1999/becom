<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailOpenTracking;

class MailOpenTrackingService extends AbstractService
{
    protected $modelClass = MailOpenTracking::class;

    /**
     * @param $mailSendingHistoryUuid
     * @param $ip
     * @param $userAgent
     * @return mixed
     */
    public function mailOpenTracking($mailSendingHistoryUuid, $ip, $userAgent)
    {
        return $this->create([
            'mail_sending_history_uuid' => $mailSendingHistoryUuid,
            'ip' => $ip,
            'user_agent' => $userAgent
        ]);
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return mixed
     */
    public function reportAnalyticDataCampaigns($fromDate, $toDate, $websiteUuid){
        if(empty($websiteUuid)){
            return $this->model->selectRaw('mail_sending_history.campaign_uuid, count(mail_open_trackings.uuid) as opened')
                ->join('mail_sending_history', 'mail_sending_history.uuid', '=', 'mail_open_trackings.mail_sending_history_uuid')
                ->whereDate('mail_open_trackings.created_at', '>=', $fromDate)
                ->whereDate('mail_open_trackings.created_at', '<=', $toDate)
                ->groupBy('mail_sending_history.campaign_uuid')->get();
        }

        return $this->model->selectRaw('mail_sending_history.campaign_uuid, count(mail_open_trackings.uuid) as opened')
            ->join('mail_sending_history', 'mail_sending_history.uuid', '=', 'mail_open_trackings.mail_sending_history_uuid')
            ->join('campaigns', 'campaigns.uuid', '=', 'mail_sending_history.campaign_uuid')
            ->where('campaigns.send_project_uuid', $websiteUuid)
            ->whereDate('mail_open_trackings.created_at', '>=', $fromDate)
            ->whereDate('mail_open_trackings.created_at', '<=', $toDate)
            ->groupBy('mail_sending_history.campaign_uuid')->get();

    }

    /**
     * @param $fromDate
     * @param $toDate
     * @param $campaignUuid
     * @return mixed
     */
    public function getNumberOpenMailByCampaignUuid($fromDate, $toDate, $campaignUuid){
        return $this->model->join('mail_sending_history', 'mail_sending_history.uuid', '=', 'mail_open_trackings.mail_sending_history_uuid')
            ->whereDate('mail_open_trackings.created_at', '>=', $fromDate)
            ->whereDate('mail_open_trackings.created_at', '<=', $toDate)
            ->where('mail_sending_history.campaign_uuid', $campaignUuid)
            ->groupBy('mail_sending_history.campaign_uuid')->count();
    }
}
