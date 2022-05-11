<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\MailSendingHistory;
use App\Models\QueryBuilders\MailSendingHistoryQueryBuilder;
use Illuminate\Support\Facades\DB;

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
}
