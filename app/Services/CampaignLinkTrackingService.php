<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CampaignLinkTracking;

class CampaignLinkTrackingService extends AbstractService
{
    protected $modelClass = CampaignLinkTracking::class;

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
    public function incrementTotalClickByCampaignUuid($campaignUuid)
    {
        $model = $this->findByCampaignUuid($campaignUuid);

        if (!empty($model)) {
            $this->update($model, array_merge(request()->all(), [
                'total_click' => $model->total_click + 1,
            ]));
        } else {
            $model = $this->create([
                'campaign_uuid' => $campaignUuid,
                'total_click' => 1,
                'to_url' => request()->get('to_url'),
            ]);
        }

        return $model;
    }
}
