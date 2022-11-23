<?php

namespace App\Console\Commands;

use App\Events\SendEmailNotOpenByScenarioCampaignEvent;
use App\Services\CampaignService;
use App\Services\ContactService;
use App\Services\MailSendingHistoryService;
use Illuminate\Console\Command;

class SendEmailNotOpenByScenarioCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:email-not-open';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email not open campaign by scenario campaign';

    /**
     * @var MailSendingHistoryService
     */
    protected $service;

    /**
     * @var CampaignService
     */
    protected $campaignService;

    protected $contactService;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        CampaignService $campaignService,
        MailSendingHistoryService $service,
        ContactService $contactService
    )
    {
        parent::__construct();
        $this->service = $service;
        $this->campaignService = $campaignService;
        $this->contactService = $contactService;
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mailNotOpenHistories = $this->service->getMailNotOpenHistories();
        $contactsNotOpenByScenarioCampaignUuid = [];
        $checkContactExist = false;
        foreach ($mailNotOpenHistories as $mailNotOpenHistory) {
            $campaign = $this->campaignService->findOneById($mailNotOpenHistory->campaign_uuid);
            $contactNotOpenMail = $this->contactService->getContactByCampaign($campaign->uuid, $mailNotOpenHistory->email);
            $campaignScenario = $this->campaignService->findOneById($campaign->not_open_mail_campaign);

            $contactListCampaignScenario = $campaignScenario->contactlists;
            $contact = $this->contactService->checkAndInsertContactIntoContactList($contactNotOpenMail, $contactListCampaignScenario[0]->uuid)->toArray();

            if ($this->campaignService->checkActiveScenarioCampaign($campaignScenario->uuid)) {
                if (($this->service->getNumberEmailSentByStatusAndCampaignUuid($campaignScenario->uuid, "sent") > 0 ||
                        $this->service->getNumberEmailSentByStatusAndCampaignUuid($campaignScenario->uuid, "opened") > 0) && ($this->service->getNumberEmailSentPerUser($campaignScenario->uuid, $contact['email']) == 0)) {
                    if (array_key_exists($campaignScenario->uuid, $contactsNotOpenByScenarioCampaignUuid)) {
                        foreach ($contactsNotOpenByScenarioCampaignUuid[$campaignScenario->uuid] as $item) {
                            if (in_array($contact['email'], $item)) {
                                $checkContactExist = true;
                                break;
                            }
                        }
                        if (!$checkContactExist) {
                            $contactsNotOpenByScenarioCampaignUuid[$campaignScenario->uuid][] = $contact;
                        }
                    }else {
                        $contactsNotOpenByScenarioCampaignUuid[$campaignScenario->uuid][] = $contact;
                    }
                }
            }
        }

        if (!empty($contactsNotOpenByScenarioCampaignUuid)) {
            SendEmailNotOpenByScenarioCampaignEvent::dispatch($contactsNotOpenByScenarioCampaignUuid);
        }
    }
}
