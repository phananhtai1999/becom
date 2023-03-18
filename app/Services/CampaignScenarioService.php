<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CampaignScenario;
use Carbon\Carbon;

class CampaignScenarioService extends AbstractService
{
    protected $modelClass = CampaignScenario::class;

    /**
     * @param $scenarioUuid
     * @return mixed
     */
    public function showCampaignScenarioByScenarioUuid($scenarioUuid)
    {
        $campaignsScenario = $this->model->where(['scenario_uuid' => $scenarioUuid])->with('campaign')->get()->toArray();

        return $campaignsScenario;
    }

    /**
     * @param $scenarioUuid
     * @return mixed
     */
    public function getMaxDepthOfCampaignScenarioByScenarioUuid($scenarioUuid)
    {
        return $this->model->where('scenario_uuid', $scenarioUuid)->orderBy('depth', "DESC")->pluck('depth')->first();
    }

    /**
     * @param $Uuid
     * @param $scenarioUuid
     * @return mixed
     */
    public function getCampaignsScenarioExistsInUUidByScenarioUuid($Uuid, $scenarioUuid)
    {
       return $this->model->where('scenario_uuid', $scenarioUuid)->whereNotIn('uuid', $Uuid)->orderBy('uuid', 'DESC')->get();
    }

    /**
     * @param $campaignScenarioUuid
     * @param $timeSendEmail
     * @return null|mixed
     */
    public function getCampaignWhenOpenEmailByUuid($campaignScenarioUuid, $timeSendEmail)
    {
        $campaignScenarioNotOpen = $this->model->where([
            ['parent_uuid', $campaignScenarioUuid],
            ['type', "not_open"]
        ])->first();
        $campaignScenarioOpen = $this->model->where([
            ['parent_uuid', $campaignScenarioUuid],
            ['type', "open"]
        ])->first();
        if ($campaignScenarioNotOpen) {
            if ($timeSendEmail->addDays($campaignScenarioNotOpen->open_within) >= Carbon::now()) {
                return $campaignScenarioOpen;
            }

            return null;
        }

        return $campaignScenarioOpen;
    }

    /**
     * @param $campaignScenarioUuid
     * @return mixed
     */
    public function getCampaignWhenNotOpenEmailByUuid($campaignScenarioUuid)
    {
        return $this->model->where([
            ['parent_uuid', $campaignScenarioUuid],
            ['type', "not_open"]
        ])->first();
    }

    /**
     * @param $scenarioUuid
     * @return mixed
     */
    public function showCampaignScenarioByCampaignUuid($campaignUuid)
    {
        return $this->model->where('campaign_uuid', $campaignUuid)->get();
    }

    public function getCampaignScenarioRootByScenarioUuid($scenarioUuid)
    {
        return $this->model->where('scenario_uuid', $scenarioUuid)->whereNull('parent_uuid')->first();
    }
}
