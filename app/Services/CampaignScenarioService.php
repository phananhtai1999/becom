<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CampaignScenario;

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
     * @return mixed
     */
    public function getCampaignWhenOpenEmailByUuid($campaignScenarioUuid)
    {
        return $this->model->where([
            ['parent_uuid', $campaignScenarioUuid],
            ['type', "open"]
        ])->first();
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
}
