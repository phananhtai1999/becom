<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Events\SendByCampaignEvent;
use Carbon\Carbon;

class SendEmailByCampaignService extends AbstractService
{
    public function sendEmailByActiveCampaign($activeCampaign)
    {
        $sendEmailScheduleLog = app(SendEmailScheduleLogService::class)->create([
                'campaign_uuid' => $activeCampaign->getKey(),
                'start_time' => Carbon::now()
        ]);

        try {
            $mailSendingHistories = $activeCampaign->mailSendingHistories;

            foreach ($mailSendingHistories as $mailSendingHistory) {
                $haveBeenSentEmails[] = $mailSendingHistory->email;
            }

            if (empty($haveBeenSentEmails)) {
                $emailsCampaign = $activeCampaign->website->emails;

                $quantityEmailWasSentPerUser = 0;

                SendByCampaignEvent::dispatch($activeCampaign, $emailsCampaign, $quantityEmailWasSentPerUser, $sendEmailScheduleLog);


            } else {
                $emails = app(EmailService::class)->getEmailInArray($haveBeenSentEmails);

                $quantityEmailWasSentPerUser = app(MailSendingHistoryService::class)->getNumberEmailSentPerUserByCampaignUuid($activeCampaign->uuid)
                    ->quantity_email_per_user;

                SendByCampaignEvent::dispatch($activeCampaign, $emails, $quantityEmailWasSentPerUser, $sendEmailScheduleLog);

            }
        }catch (\Exception $e){
            app(SendEmailScheduleLogService::class)->update($sendEmailScheduleLog,[
                'was_crashed' => true,
                'log' => $e->getMessage()
            ]);
        }

    }
}
