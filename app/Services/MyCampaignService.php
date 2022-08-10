<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\MyCampaignQueryBuilder;
use Carbon\Carbon;

class MyCampaignService extends AbstractService
{
    protected $modelClass = Campaign::class;

    protected $modelQueryBuilderClass = MyCampaignQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function findMyCampaignByKeyOrAbort($id)
    {
        $campaign = $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['campaigns.uuid', $id]
            ])->first();

        if (!empty($campaign)) {
            return $campaign;
        } else {
            abort(403, 'Unauthorized.');
        }
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyCampaignByKey($id)
    {
        $campaign = $this->findMyCampaignByKeyOrAbort($id);

        return $this->destroy($campaign->getKey());
    }

    /**
     * @return mixed
     */
    public function loadActiveMyCampaign()
    {
        return $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->whereNotIn('campaigns.uuid', function ($query){
                $query->select('campaigns.uuid')
                    ->from('campaigns')
                    ->join('send_email_schedule_logs', 'send_email_schedule_logs.campaign_uuid', '=', 'campaigns.uuid')
                    ->where('send_email_schedule_logs.is_running', true);
            })->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['campaigns.from_date', '<=', Carbon::now()],
                ['campaigns.to_date', '>=', Carbon::now()],
                ['campaigns.was_finished', false],
                ['campaigns.was_stopped_by_owner', false],
            ])->firstOrFail();
    }
}
