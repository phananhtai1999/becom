<?php

namespace App\Listeners;

use App\Events\SendByBirthdayCampaignEvent;
use App\Notifications\BaseNotification;
use App\Services\CampaignService;
use Techup\ApiConfig\Services\ConfigService;
use App\Services\ContactService;
use App\Services\CreditHistoryService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendByBirthdayCampaignListener implements ShouldQueue
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
     * @var SendEmailScheduleLogService
     */
    private $sendEmailScheduleLogService;

    /**
     * @var ContactService
     */
    private $contactService;

    /**
     * @var UserService
     */
    private $userService;

    /**
     * @var CreditHistoryService
     */
    private $creditHistoryService;

    /**
     * @var ConfigService
     */
    private $configService;

    /**
     * @var int
     */
    private $creditReturn = 0;

    /**
     * @param MailTemplateVariableService $mailTemplateVariableService
     * @param MailSendingHistoryService $mailSendingHistoryService
     * @param SmtpAccountService $smtpAccountService
     * @param CampaignService $campaignService
     * @param EmailService $emailService
     * @param SendEmailScheduleLogService $sendEmailScheduleLogService
     * @param ContactService $contactService
     * @param UserService $userService
     * @param CreditHistoryService $creditHistoryService
     * @param ConfigService $configService
     */
    public function __construct(
        MailTemplateVariableService $mailTemplateVariableService,
        MailSendingHistoryService   $mailSendingHistoryService,
        SmtpAccountService          $smtpAccountService,
        CampaignService $campaignService,
        EmailService $emailService,
        SendEmailScheduleLogService $sendEmailScheduleLogService,
        ContactService $contactService,
        UserService $userService,
        CreditHistoryService $creditHistoryService,
        ConfigService $configService
    )
    {
        $this->mailTemplateVariableService = $mailTemplateVariableService;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->smtpAccountService = $smtpAccountService;
        $this->campaignService = $campaignService;
        $this->emailService = $emailService;
        $this->sendEmailScheduleLogService = $sendEmailScheduleLogService;
        $this->contactService = $contactService;
        $this->userService = $userService;
        $this->creditHistoryService = $creditHistoryService;
        $this->configService = $configService;
    }

    /**
     * @param SendByBirthdayCampaignEvent $event
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(SendByBirthdayCampaignEvent $event)
    {
        $listBirthdayCampaign = $event->listBirthdayCampaign;

        foreach ($listBirthdayCampaign as $campaign) {
            $this->creditReturn = 0;
            $emailNotification =  app()->makeWith(BaseNotification::class, ['campaign' => $campaign])->getAdapter();
            $contacts = $emailNotification->getBirthdayContacts();
            $creditNumberSendByCampaign = count($contacts) * $emailNotification->getNotificationPrice();

            if (!$this->userService->checkCredit($creditNumberSendByCampaign, $campaign->user_uuid)){
                $this->campaignService->update($campaign, [
                    'was_stopped_by_owner' => true
                ]);
                continue;
            }
            $config = $this->configService->findConfigByKey('send_by_connector');
            if ($config && $config->value_formatted) {
                $emailNotification->sending_by_conecttor($contacts, null, $creditNumberSendByCampaign);
            } else {

                $emailNotification->send($contacts, null, $creditNumberSendByCampaign);
            }
        }
    }
}
