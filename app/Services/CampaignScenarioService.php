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
}
