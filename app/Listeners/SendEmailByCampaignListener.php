<?php

namespace App\Listeners;

use App\Events\SendEmailByCampaignEvent;
use App\Mail\SendCampaign;
use App\Services\CampaignService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SmtpAccountService;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class SendEmailByCampaignListener implements ShouldQueue
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
     * @var EmailService
     */
    private $emailService;

    /**
     * @param MailTemplateVariableService $mailTemplateVariableService
     * @param MailSendingHistoryService $mailSendingHistoryService
     * @param SmtpAccountService $smtpAccountService
     * @param CampaignService $campaignService
     * @param EmailService $emailService
     */
    public function __construct(
        MailTemplateVariableService $mailTemplateVariableService,
        MailSendingHistoryService   $mailSendingHistoryService,
        SmtpAccountService          $smtpAccountService,
        CampaignService $campaignService,
        EmailService $emailService
    )
    {
        $this->mailTemplateVariableService = $mailTemplateVariableService;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->smtpAccountService = $smtpAccountService;
        $this->campaignService = $campaignService;
        $this->emailService = $emailService;

    }

    /**
     * Handle the event.
     *
     * @param SendEmailByCampaignEvent $event
     * @return void
     * @throws \Throwable
     */
    public function handle(SendEmailByCampaignEvent $event)
    {
        $campaign = $event->campaign;
        $toEmails = $event->toEmails;
        $isSaveHistory = $event->isSaveHistory;

        $this->smtpAccountService->setSmtpAccountForCampaign($campaign->smtpAccount);
        if($isSaveHistory){
            $this->campaignService->update($campaign, ['is_running' => true]);
            $this->sendEmailByCampaign($campaign, $toEmails);
            $this->campaignService->update($campaign, ['is_running' => false]);
        }else{
            $emails = $this->emailService->getEmailInArray($toEmails);
            foreach ($emails as $email){
                $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $email, $campaign->smtpAccount, $campaign);

                Mail::to($email->email)->send(new SendCampaign($mailTemplate));
            }
        }
    }

    /**
     * @param $campaign
     * @param $toEmails
     * @return void
     */
    public function sendEmailByCampaign($campaign, $toEmails)
    {
        if(empty($toEmails)){
            $emails = $campaign->website->emails;
        }else{
            $emails = $this->emailService->getEmailInArray($toEmails);
        }
        for ($i = 1; $i <= $campaign->number_email_per_date; $i++) {
            foreach ($emails as $email){
                $quantityEmailWasSentPerUser = $this->mailSendingHistoryService->getNumberEmailSentPerUser($campaign->uuid, $email->email);

                if($quantityEmailWasSentPerUser < $campaign->number_email_per_user){
                    $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $email, $campaign->smtpAccount, $campaign);

                    Mail::to($email->email)->send(new SendCampaign($mailTemplate));

                    $this->mailSendingHistoryService->create([
                        'email' => $email->email,
                        'campaign_uuid' => $campaign->uuid,
                        'time' => Carbon::now()
                    ]);
                }
            }
        }

        if($this->checkWasFinishedCampaign($campaign)){
            $this->campaignService->update($campaign, ['was_finished' => true]);
        }

    }

    /**
     * @param $campaign
     * @return bool
     */
    public function checkWasFinishedCampaign($campaign)
    {
        $emails = $this->emailService->findAllWhere(['website_uuid' => $campaign->website_uuid]);
        foreach ($emails as $email){
            if($this->mailSendingHistoryService->getNumberEmailSentPerUser($campaign->uuid, $email->email) !== $campaign->number_email_per_user){
                return false;
            }
        }
        return true;
    }

}
