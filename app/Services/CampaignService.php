<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use Carbon\Carbon;

class CampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    protected $modelQueryBuilderClass = CampaignQueryBuilder::class;

    /**
     * @return mixed|void
     */
    public function loadActiveCampaign()
    {
        return $this->model->select('campaigns.*')
                ->whereNotIn('uuid', function ($query){
                    $query->select('campaigns.uuid')
                        ->from('campaigns')
                        ->join('send_email_schedule_logs', 'send_email_schedule_logs.campaign_uuid', '=', 'campaigns.uuid')
                        ->where('send_email_schedule_logs.is_running', true);
                })->where([
                ['campaigns.from_date', '<=', Carbon::now()],
                ['campaigns.to_date', '>=', Carbon::now()],
                ['campaigns.was_finished', false],
                ['campaigns.was_stopped_by_owner', false],
            ])->firstOrFail();
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function findCampaignByReport($campaignUuid)
    {
        return $this->model->where('uuid', $campaignUuid)->withTrashed()->first();
    }

}
