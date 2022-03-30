<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CampaignLinkDailyTracking;
use Carbon\Carbon;

class CampaignLinkDailyTrackingService extends AbstractService
{
    protected $modelClass = CampaignLinkDailyTracking::class;

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function firstByCampaignUuid($campaignUuid)
    {
        return $this->model->where('campaign_uuid', $campaignUuid)
            ->whereDate('date', Carbon::now())
            ->first();
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function incrementTotalClickByCampaignUuid($campaignUuid)
    {
        $model = $this->firstByCampaignUuid($campaignUuid);

        if (!empty($model)) {
            $this->update($model, array_merge(request()->all(), [
                'total_click' => $model->total_click + 1,
            ]));
        } else {
            $model = $this->create([
                'campaign_uuid' => $campaignUuid,
                'total_click' => 1,
                'to_url' => request()->get('to_url'),
                'date' => Carbon::now(),
            ]);
        }

        return $model;
    }
}
