<?php

namespace App\Listeners;

use App\Events\SendCampaignByEmailEvent;
use App\Mail\SendCampaign;
use App\Services\CampaignService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SmtpAccountService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendCampaignByEmailListener implements ShouldQueue
{
    /**
     * @var MailTemplateVariableService
     */
    private $mailTemplateVariableService;

    /**
     * @var MailSendingHistoryService
     */
    private $mailSendingHistoryService;

    /**
     * @var SmtpAccountService
     */
    private $smtpAccountService;

    /**
     * @var CampaignService
     */
    private $campaignService;

    /**
     * Create the event listener.
     *
     * @param MailTemplateVariableService $mailTemplateVariableService
     * @param MailSendingHistoryService $mailSendingHistoryService
     * @param SmtpAccountService $smtpAccountService
     */
    public function __construct(
        MailTemplateVariableService $mailTemplateVariableService,
        MailSendingHistoryService   $mailSendingHistoryService,
        SmtpAccountService          $smtpAccountService,
        CampaignService $campaignService
    )
    {
        $this->mailTemplateVariableService = $mailTemplateVariableService;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->smtpAccountService = $smtpAccountService;
        $this->campaignService = $campaignService;

    }

    /**
     * Handle the event.
     *
     * @param SendCampaignByEmailEvent $event
     * @return void
     * @throws \Throwable
     */
    public function handle(SendCampaignByEmailEvent $event)
    {
        $activeCampaign = $event->campaign;
        $emails = $event->emails;
        $quantityEmailWasSentPerUser = $event->quantityEmailWasSentPerUser;

        $this->smtpAccountService->setSmtpAccountForCampaign($activeCampaign->smtpAccount);

        $this->sendActiveCampaignByEmail($activeCampaign, $emails, $quantityEmailWasSentPerUser);

        $this->campaignService->update($activeCampaign, ['is_running' => false]);

    }

    public function sendActiveCampaignByEmail($campaign, $emails, $quantityEmailWasSentPerUser)
    {
        for ($i = 1; $i <= $campaign->number_email_per_date; $i++) {

            if ($quantityEmailWasSentPerUser < $campaign->number_email_per_user) {

                foreach ($emails as $email) {
                    $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $email, $campaign->smtpAccount, $campaign);

                    Mail::to($email->email)->send(new SendCampaign($mailTemplate));

                    $this->mailSendingHistoryService->create([
                        'email' => $email->email,
                        'campaign_uuid' => $campaign->uuid,
                        'time' => Carbon::now()
                    ]);
                }

                $quantityEmailWasSentPerUser++;

            }
        }

        if ($quantityEmailWasSentPerUser === $campaign->number_email_per_user) {
            $this->campaignService->update($campaign, ['was_finished' => true]);
        }
    }
}
