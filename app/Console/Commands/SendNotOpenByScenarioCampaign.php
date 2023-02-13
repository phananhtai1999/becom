<?php

namespace App\Console\Commands;

use App\Events\SendNotOpenByScenarioCampaignEvent;
use App\Services\CampaignScenarioService;
use App\Services\CampaignService;
use App\Services\ContactService;
use App\Services\MailSendingHistoryService;
use Illuminate\Console\Command;

class SendNotOpenByScenarioCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:not-open-campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send not open campaign by scenario campaign';

    /**
     * @var MailSendingHistoryService
     */
    protected $service;

    /**
     * @var CampaignService
     */
    protected $campaignService;

    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * @var CampaignScenarioService
     */
    protected $campaignScenarioService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        CampaignService           $campaignService,
        MailSendingHistoryService $service,
        ContactService            $contactService,
        CampaignScenarioService   $campaignScenarioService
    )
    {
        parent::__construct();
        $this->service = $service;
        $this->campaignService = $campaignService;
        $this->contactService = $contactService;
        $this->campaignScenarioService = $campaignScenarioService;
    }

    /**
     * @return void
     */
    public function handle()
    {
        $notOpenHistories = $this->service->getMailNotOpenHistories();
        $contactNotOpenByCampaignScenario = [];
        foreach ($notOpenHistories->groupBy('campaign_scenario_uuid') as $campaignScenarioUuid => $mailSendingHistories) {
            $contacts = [];
            $notOpenCampaignScenario = $this->campaignScenarioService->getCampaignWhenNotOpenEmailByUuid($campaignScenarioUuid);

            if ($campaign = $this->campaignService->checkActiveCampaignScenario($notOpenCampaignScenario->campaign_uuid)) {
                foreach ($mailSendingHistories->pluck('email') as $emailNotOpen) {

                    $contactNotOpen = $this->contactService->getContactByCampaign(optional($mailSendingHistories[0]->campaignScenario)->getRoot()->campaign_uuid, $emailNotOpen);
                    $contacts[] = $contactNotOpen;
                }

                $contactNotOpenByCampaignScenario[] = [
                    "campaign" => $campaign,
                    "contact" => $contacts,
                    "campaignScenario" => $notOpenCampaignScenario
                ];
            }
        }

        if (!empty($contactNotOpenByCampaignScenario)) {
            SendNotOpenByScenarioCampaignEvent::dispatch($contactNotOpenByCampaignScenario);
        }
    }
}
