<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;
use App\Models\QueryBuilders\MailSendingHistoryQueryBuilder;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\Types\True_;

class MailSendingHistoryService extends AbstractService
{
    protected $modelClass = MailSendingHistory::class;

    protected $modelQueryBuilderClass = MailSendingHistoryQueryBuilder::class;

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getNumberEmailSentPerUserByCampaignUuid($campaignUuid)
    {
        return $this->model->select('email', DB::raw('COUNT(email) AS quantity_email_per_user'))
            ->where('campaign_uuid', $campaignUuid)
            ->groupBy('email')
            ->first();
    }

    /**
     * @param $campaignUuid
     * @param $email
     * @return mixed
     */
    public function getNumberEmailSentPerUser($campaignUuid, $email)
    {
        return $this->model->where('campaign_uuid', $campaignUuid)
            ->where('email', $email)
            ->whereNotIn('status', ["fail"])
            ->groupBy('email')
            ->count();
    }

    /**
     * @param $campaign
     * @param $emails
     * @return bool
     */
    public function checkTodayNumberEmailSentUser($campaign, $toEmails){
        foreach($toEmails as $email){
            $numberEmailSent =  $this->model->where('campaign_uuid', $campaign->uuid)
                ->where('email', $email)
                ->whereDate('time', Carbon::now())
                ->groupBy('email')
                ->count();

            if($numberEmailSent >= $campaign->number_email_per_date){
                return false;
            }
        }

        return true;
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function getNumberEmailSentByCampaign($campaignUuid)
    {
        return $this->model->where('campaign_uuid', $campaignUuid)->groupBy('campaign_uuid')->count();
    }

    /**
     * @param $campaignUuid
     * @param $status
     * @return mixed
     */
    public function getNumberEmailSentByStatusAndCampaignUuid($campaignUuid, $status)
    {
        return $this->model->where([
            ['campaign_uuid', $campaignUuid],
            ['status', $status]
        ])->count();
    }
}
