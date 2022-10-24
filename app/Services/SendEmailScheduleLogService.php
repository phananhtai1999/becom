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

}
