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

}
