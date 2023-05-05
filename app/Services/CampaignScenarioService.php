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
        return $this->model->where(['scenario_uuid' => $scenarioUuid])->with('campaign')->get()->toArray();
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

    public function getListCreditByLevelScenario($scenarioUuid, $sort)
    {
        $campaignsScenario = $this->showCampaignScenarioByScenarioUuid($scenarioUuid);
        return collect($campaignsScenario)->groupBy(function ($item) {
            return $item['depth'] + 1;
        })->map(function ($items) use ($sort){
            $max = 0;
            foreach ($items as $item) {
                $credit = $sort[$item['campaign']['send_type']]['value'];
                if ($credit > $max) {
                    $max = $credit;
                }
            }
            return $max;
        });
    }

    public function calculateNumberCreditOfScenario($scenarioUuid)
    {
        $campaignRootScenario = $this->getCampaignScenarioRootByScenarioUuid($scenarioUuid);
        $listPriceByType = (new ConfigService())->getListPriceByType();
        $listCreditByLevelScenario = $this->getListCreditByLevelScenario($scenarioUuid, $listPriceByType);
        $numberContact = (new ContactService())->getListsContactsSendSmsByCampaigns($campaignRootScenario->campaign_uuid);
        $creditNumberSendEmail = 0;
        foreach ($listCreditByLevelScenario as $item){
            $creditNumberSendEmail = $creditNumberSendEmail + $numberContact * $item;
        }
        return $creditNumberSendEmail;
    }
}
