<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\SendEmailScheduleLog;
use Carbon\Carbon;

class SendEmailScheduleLogService extends AbstractService
{
    protected $modelClass = SendEmailScheduleLog::class;

    /**
     * @param $campaignUuid
     * @return bool
     */
    public function checkActiveCampaignbyCampaignUuid($campaignUuid)
    {
        $sendEmailScheduleLog = $this->findOneWhere([
            ['campaign_uuid', $campaignUuid],
            ['is_running', true]
        ]);

        if ($sendEmailScheduleLog){
            return false;
        }
        return true;
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getTotalRunningCampaignChart($startDate, $endDate)
    {
        return $this->model->whereDate('updated_at', '>=', $startDate)
            ->whereDate('updated_at', '<=', $endDate)
            ->where('is_running', true)->count();
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return mixed
     */
    public function getTotalRunningMyCampaignChart($startDate, $endDate)
    {
         return $this->model->join('campaigns', 'campaigns.uuid', '=', 'send_email_schedule_logs.campaign_uuid')
            ->whereDate('send_email_schedule_logs.updated_at', '>=', $startDate)
            ->whereDate('send_email_schedule_logs.updated_at', '<=', $endDate)
             ->where([
                 'campaigns.user_uuid' => auth()->userId(),
                 'campaigns.app_id' => auth()->appId(),
                 'send_email_schedule_logs.is_running' => true
             ])->count();
    }

}
