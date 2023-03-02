<?php

namespace App\Notifications;

use App\Services\ConfigService;
use App\Services\ContactService;
use App\Services\CreditHistoryService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class ViberNotification extends BaseNotification {

    /**
     * @var
     */
    protected $campaign;

    /**
     * @var ConfigService
     */
    protected $configService;

    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * protected $smtpAccountService;
     * @var SendEmailScheduleLogService
     */
    protected $sendEmailScheduleLogService;

    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @var CreditHistoryService
     */
    protected $creditHistoryService;

    /**
     * @var MailSendingHistoryService
     */
    protected $mailSendingHistoryService;

    /**
     * @var MailTemplateVariableService
     */
    protected $mailTemplateVariableService;

    /**
     * @param $campaign
     * @param ConfigService $configService
     * @param ContactService $contactService
     * @param SmtpAccountService $smtpAccountService
     * @param SendEmailScheduleLogService $sendEmailScheduleLogService
     * @param UserService $userService
     * @param CreditHistoryService $creditHistoryService
     * @param MailSendingHistoryService $mailSendingHistoryService
     * @param MailTemplateVariableService $mailTemplateVariableService
     */
    public function __construct(
        $campaign,
        ConfigService $configService,
        ContactService $contactService,
        SmtpAccountService $smtpAccountService,
        SendEmailScheduleLogService $sendEmailScheduleLogService,
        UserService $userService,
        CreditHistoryService $creditHistoryService,
        MailSendingHistoryService $mailSendingHistoryService,
        MailTemplateVariableService $mailTemplateVariableService
    )
    {
        $this->campaign = $campaign;
        $this->configService = $configService;
        $this->contactService = $contactService;
        $this->smtpAccountService = $smtpAccountService;
        $this->sendEmailScheduleLogService = $sendEmailScheduleLogService;
        $this->userService = $userService;
        $this->creditHistoryService = $creditHistoryService;
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->mailTemplateVariableService = $mailTemplateVariableService;
    }

    /**
     * @return array
     */
    public function getBirthdayContacts()
    {
        return $this->contactService->getBirthdayContactsSendSms($this->campaign->uuid);
    }

    /**
     * @return mixed
     */
    public function getContacts()
    {
        return $this->contactService->getContactsSendSms($this->campaign->uuid);
    }

    /**
     * @return mixed
     */
    public function calculatorCredit()
    {
        return $this->campaign->number_credit_needed_to_start_campaign;
    }

    /**
     * @return mixed
     */
    public function getNotificationPrice()
    {
        return $this->configService->findConfigByKey('viber_price')->value;
    }

    /**
     * @param $contact
     * @param $smtpAccount
     * @param $mailSendingHistory
     * @return mixed
     */
    public function getContent($contact, $smtpAccount, $mailSendingHistory)
    {
        $mailTemplate = $this->mailTemplateVariableService->renderBody($this->campaign->mailTemplate, $contact, $smtpAccount, $this->campaign);
        return $mailTemplate->body;
    }

    /**
     * @param $contact
     * @param $scenario
     * @return mixed
     */
    public function saveMailSendingHistory($contact, $scenario)
    {
        return $this->mailSendingHistoryService->create([
            'email' => $contact->phone,
            'campaign_uuid' => $this->campaign->uuid,
            'campaign_scenario_uuid' => $scenario,
            'time' => Carbon::now()
        ]);
    }

    /**
     * @param $contact
     * @param $content
     * @param $smtpAccount
     * @param $mailTemplate
     * @return void
     */
    public function sendContent($contact, $content, $smtpAccount, $mailTemplate)
    {
        Log::info('Phone:' . "$contact->phone" . '|' . 'Content:' . "$content". '|' . 'secret_ket:' . "$smtpAccount->secret_key" . '|' . 'image:' . json_encode($mailTemplate->image));
    }

    /**
     * @param $user
     * @return mixed
     */
    public function getSendOption($user)
    {
        $configSmtpAuto = $this->configService->findConfigByKey('smtp_auto');
        if ($user->can_add_smtp_account == 1 || $configSmtpAuto->value == 0) {
            if (!empty($this->campaign->smtpAccount)) {
                $smtpAccount = $this->campaign->smtpAccount;
            } else {
                $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin($this->campaign->send_type);
            }
        } else {
            $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin($this->campaign->send_type);
        }
        $this->smtpAccountService->setSwiftSmtpAccountForSendEmail($smtpAccount);

        return $smtpAccount;
    }
}


