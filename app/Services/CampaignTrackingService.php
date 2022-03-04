<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CampaignTracking;

class CampaignTrackingService extends AbstractService
{
    protected $modelClass = CampaignTracking::class;

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function findByCampaignUuid($campaignUuid)
    {
        return $this->model->where('campaign_uuid', $campaignUuid)
            ->first();
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function incrementTotalOpenByCampaignUuid($campaignUuid)
    {
        $model = $this->findByCampaignUuid($campaignUuid);

        if (!empty($model)) {
            $this->update($model, [
                'total_open' => $model->total_open + 1,
            ]);
        } else {
            $model = $this->create([
                'campaign_uuid' => $campaignUuid,
                'total_open' => 1,
                'total_link_click' => 0,
            ]);
        }

        return $model;
    }
}
