<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\QueryBuilders\MyCampaignQueryBuilder;

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
     * @return mixed|void
     */
    public function deleteMyCampaignByKey($id)
    {
        $campaign = $this->model->select('campaigns.*')
            ->join('websites', 'websites.uuid', '=', 'campaigns.website_uuid')
            ->where([
                ['websites.user_uuid', auth()->user()->getKey()],
                ['campaigns.uuid', $id]
            ])->first();

        if (!empty($campaign)) {
            return $this->destroy($campaign->getKey());
        } else {
            abort(403, 'Unauthorized.');
        }
    }
}
