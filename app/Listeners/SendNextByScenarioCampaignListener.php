<?php

namespace App\Listeners;

use App\Events\SendNextByScenarioCampaignEvent;
use App\Notifications\BaseNotification;
use App\Services\CampaignService;
use App\Services\ConfigService;
use App\Services\ContactService;
use App\Services\CreditHistoryService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Illuminate\Contracts\Queue\ShouldQueue;

class SendNextByScenarioCampaignListener implements  ShouldQueue
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
        CampaignService             $campaignService,
        EmailService                $emailService,
        SendEmailScheduleLogService $sendEmailScheduleLogService,
        ContactService              $contactService,
        UserService                 $userService,
        CreditHistoryService        $creditHistoryService,
        ConfigService               $configService
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
     * @param SendNextByScenarioCampaignEvent $event
     * @return void
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function handle(SendNextByScenarioCampaignEvent $event)
    {
        $campaignScenario = $event->campaignScenario;
        $contact = $event->contact;
        $campaign = $event->campaign;

        $emailNotification = app()->makeWith(BaseNotification::class, ['campaign' => $campaign])->getAdapter();

        $config = $this->configService->findConfigByKey('send_by_connector');
        if ($config && $config->value_formatted) {
            $emailNotification->sending_by_conecttor($contact, $campaignScenario->uuid, null);
        } else {
            $emailNotification->send($contact, $campaignScenario->uuid, null);
        }
    }
}
