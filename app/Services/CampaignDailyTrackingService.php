<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CampaignDailyTracking;
use Carbon\Carbon;

class CampaignDailyTrackingService extends AbstractService
{
    protected $modelClass = CampaignDailyTracking::class;

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
    public function incrementTotalOpenByCampaignUuid($campaignUuid)
    {
        $model = $this->firstByCampaignUuid($campaignUuid);

        if (!empty($model)) {
            $this->update($model, [
                'total_open' => $model->total_open + 1,
            ]);
        } else {
            $model = $this->create([
                'campaign_uuid' => $campaignUuid,
                'total_open' => 1,
                'total_link_click' => 0,
                'date' => Carbon::now(),
            ]);
        }

        return $model;
    }

    /**
     * @param $campaignUuid
     * @return mixed
     */
    public function incrementTotalLinkClickByCampaignUuid($campaignUuid)
    {
        $model = $this->firstByCampaignUuid($campaignUuid);

        if (!empty($model)) {
            $this->update($model, [
                'total_link_click' => $model->total_link_click + 1,
            ]);
        } else {
            $model = $this->create([
                'campaign_uuid' => $campaignUuid,
                'total_open' => 0,
                'total_link_click' => 1,
                'date' => Carbon::now(),
            ]);
        }

        return $model;
    }

    /**
     * @param $fromDate
     * @param $toDate
     * @return mixed
     */
    public function loadCampaignDailyTrackingAnalytic($fromDate, $toDate)
    {
        return $this->model
            ->whereBetween('date', [$fromDate, $toDate])
            ->get();
    }
}
