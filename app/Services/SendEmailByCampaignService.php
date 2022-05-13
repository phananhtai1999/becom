<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\SendEmailByCampaignEvent;

class SendEmailByCampaignService extends AbstractService
{
    public function sendEmailByActiveCampaign($activeCampaign)
    {
        $this->update($activeCampaign, ['is_running' => true]);

        $mailSendingHistories = $activeCampaign->mailSendingHistories;

        foreach ($mailSendingHistories as $mailSendingHistory) {
            $haveBeenSentEmails[] = $mailSendingHistory->email;
        }

        if (empty($haveBeenSentEmails)) {
            $emailsCampaign = $activeCampaign->website->emails;

            $quantityEmailWasSentPerUser = 0;

            SendEmailByCampaignEvent::dispatch($activeCampaign, $emailsCampaign, $quantityEmailWasSentPerUser);


        } else {
            $emails = app(EmailService::class)->getEmailInArray($haveBeenSentEmails);

            $quantityEmailWasSentPerUser = app(MailSendingHistoryService::class)->getNumberEmailSentPerUserByCampaignUuid($activeCampaign->uuid)
                ->quantity_email_per_user;

            SendEmailByCampaignEvent::dispatch($activeCampaign, $emails, $quantityEmailWasSentPerUser);

        }
    }
}
