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
        return $this->model->where(['scenario_uuid' => $scenarioUuid])->with('campaign')->get();
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
        //Check lại phần khi open mail
        // ví dụ Mail A(1) -> stop -> run lại -> Mail A(2)->khi open mailA(1)->vẫn gửi open_campaign_mail_A(1)
        //Chỉ gửi tiếp cho những mail sau stop scenario thôi
        $campaignScenarioNotOpen = $this->model->where([
            ['parent_uuid', $campaignScenarioUuid],
            ['type', "not_open"]
        ])->first();
        $campaignScenarioOpen = $this->model->with('scenario')->where([
            ['parent_uuid', $campaignScenarioUuid],
            ['type', "open"]
        ])->first();
        $scenario = $campaignScenarioOpen->scenario;
        $checkTimeScenario = $scenario->last_stopped_at ?? $scenario->created_at;
        if (($timeSendEmail >= $checkTimeScenario) &&
            (!$campaignScenarioNotOpen||($timeSendEmail->addDays($campaignScenarioNotOpen->open_within) >= Carbon::now()))) {
            return $campaignScenarioOpen;
        }
        return null;
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

    public function getListCreditByLevelScenario($campaignsScenario, $sort)
    {
        return $campaignsScenario->groupBy(function ($item) {
            return $item['depth'] + 1;
        })->map(function ($items) use ($sort){
            $max = 0;
            foreach ($items as $item) {
                $credit = $item['campaign'] ? $sort[$item['campaign']['send_type']]['value'] : 0;
                if ($credit > $max) {
                    $max = $credit;
                }
            }
            return $max;
        });
    }

    public function calculateNumberCreditOfScenario($scenarioUuid)
    {
        $listPriceByType = (new ConfigService())->getListPriceByType();
        $campaignsScenario = $this->showCampaignScenarioByScenarioUuid($scenarioUuid);
        $campaignRootScenario = $campaignsScenario->where('parent_uuid', null)->first();
        $listCreditByLevelScenario = $this->getListCreditByLevelScenario($campaignsScenario, $listPriceByType);
        $numberContact = (new ContactService())->getListsContactsSendEmailsByCampaigns($campaignRootScenario->campaign_uuid);
        $creditNumberSendEmail = 0;
        foreach ($listCreditByLevelScenario as $item){
            $creditNumberSendEmail += ($numberContact * $item);
        }
        return $creditNumberSendEmail;
    }
}
