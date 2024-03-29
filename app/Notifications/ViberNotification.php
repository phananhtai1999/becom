<?php

namespace App\Notifications;

use Techup\ApiConfig\Services\ConfigService;
use App\Services\ContactService;
use App\Services\CreditHistoryService;
use App\Services\FooterTemplateService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use App\Models\MailSendingHistory;

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

    protected $footerTemplateService;

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
     * @param FooterTemplateService $footerTemplateService
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
        MailTemplateVariableService $mailTemplateVariableService,
        FooterTemplateService $footerTemplateService
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
        $this->footerTemplateService = $footerTemplateService;
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
     * @param $mailTemplate
     * @param $campaignContent
     * @param $variables
     * @return mixed
     */
    public function getContent($mailTemplate, $campaignContent, $variables)
    {
        return ($this->mailTemplateVariableService->renderBodyForSendCampaign($mailTemplate, $campaignContent, $variables))->rendered_body;
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
            'time' => Carbon::now(),
            'status' => MailSendingHistory::PROCESSING
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


