<?php

namespace App\Console\Commands;

use App\Events\SendCampaignByEmailEvent;
use App\Services\CampaignService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use Illuminate\Console\Command;

class SendCampaignByEmail extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email by campaign';

    /**
     * @var CampaignService
     */
    protected $service;

    /**
     * @var MailSendingHistoryService
     */
    protected $mailSendingHistoryService;

    /**
     * @var EmailService
     */
    protected $emailService;

    /**
     * Create a new command instance.
     *
     * @param CampaignService $service
     * @param MailSendingHistoryService $mailSendingHistoryService
     * @param EmailService $emailService
     *
     */
    public function __construct(
        CampaignService $service,
        MailSendingHistoryService $mailSendingHistoryService,
        EmailService $emailService
    )
    {
        $this->service = $service;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->emailService = $emailService;
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $activeCampaign = $this->service->loadActiveCampaign();

        $this->service->update($activeCampaign, ['is_running' => true]);

        $mailSendingHistories = $activeCampaign->mailSendingHistories;

        foreach ($mailSendingHistories as $mailSendingHistory) {
            $haveBeenSentEmails[] = $mailSendingHistory->email;
        }

        if (empty($haveBeenSentEmails)) {
            $emailsCampaign = $activeCampaign->website->emails;

            $quantityEmailWasSentPerUser = 0;

            SendCampaignByEmailEvent::dispatch($activeCampaign, $emailsCampaign, $quantityEmailWasSentPerUser);


        } else {
            $emails = $this->emailService->getEmailInArray($haveBeenSentEmails);

            $quantityEmailWasSentPerUser = $this->mailSendingHistoryService->getNumberEmailSentPerUserByCampaignUuid($activeCampaign->uuid)
                ->quantity_email_per_user;

            SendCampaignByEmailEvent::dispatch($activeCampaign, $emails, $quantityEmailWasSentPerUser);

        }
    }
}
