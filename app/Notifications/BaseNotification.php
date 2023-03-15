<?php

namespace App\Notifications;

use App\Services\ConfigService;
use App\Services\ContactService;
use App\Services\CreditHistoryService;
use App\Services\FooterTemplateService;
use App\Services\MailSendingHistoryService;
use App\Services\MailTemplateVariableService;
use App\Services\SendEmailScheduleLogService;
use App\Services\SmtpAccountService;
use App\Services\UserService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BaseNotification
{

    /**
     * @var int
     */
    private $creditReturn = 0;

    /**
     * @var bool
     */
    private $mailSuccess = true;

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
     * @var SmtpAccountService
     */
    protected $smtpAccountService;

    /**
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
     * @param $contacts
     * @param $scenario
     * @param $creditTotal
     * @return void
     */
    public function send($contacts, $scenario, $creditTotal)
    {
        $user = $this->campaign->user;

        if (!empty($creditTotal))
        {
            $creditNumberSendByCampaign = $creditTotal;
        } else {
            $creditNumberSendByCampaign = $this->calculatorCredit();
        }
        $configPrice = $this->getNotificationPrice();
        $smtpAccount = $this->getSendOption($user);

        $sendEmailScheduleLog = $this->sendEmailScheduleLogService->create([
            'campaign_uuid' => $this->campaign->getKey(),
            'start_time' => Carbon::now()
        ]);

        DB::beginTransaction();

        try {
            $this->userService->update($user, [
                'credit' => $user->credit - $creditNumberSendByCampaign
            ]);

            $creditHistory = $this->creditHistoryService->create([
                'user_uuid' => $this->campaign->user_uuid,
                'campaign_uuid' => $this->campaign->uuid,
                'credit' => $creditNumberSendByCampaign,
                'type' => $this->campaign->send_type
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }

        foreach ($contacts as $contact) {
            $mailSendingHistory = $this->saveMailSendingHistory($contact, $scenario);
            $emailTracking = $this->getContent($contact, $smtpAccount, $mailSendingHistory);
            try {
                $this->sendContent($contact, $emailTracking, $smtpAccount, $this->campaign->mailTemplate);
            } catch (\Exception $e) {
                $this->mailSuccess = false;
                $this->creditReturn += $configPrice;
                $this->mailSendingHistoryService->update($mailSendingHistory, [
                    'status' => 'fail'
                ]);
                $this->sendEmailScheduleLogService->update($sendEmailScheduleLog, [
                    'is_running' => false,
                    'was_crashed' => true,
                    'log' => $e->getMessage()
                ]);
            }
        }

        if ($this->creditReturn > 0) {
            $returnUser = $this->creditReturn;
            $payCreditHistory = $creditNumberSendByCampaign - $this->creditReturn;
            $this->returnCreditUserAndCreditHistory($user, $creditHistory, $returnUser, $payCreditHistory);
        }

        if ($this->mailSuccess) {
            $this->sendEmailScheduleLogService->update($sendEmailScheduleLog, [
                'end_time' => Carbon::now(),
                'is_running' => false
            ]);
        }
    }

    /**
     * @param $user
     * @param $creditHistory
     * @param $returnUser
     * @param $payCreditHistory
     * @return void
     */
    public function returnCreditUserAndCreditHistory($user, $creditHistory, $returnUser, $payCreditHistory)
    {
        DB::beginTransaction();

        try {
            $this->userService->update($user, [
                'credit' => $user->credit + $returnUser
            ]);

            $this->creditHistoryService->update($creditHistory, [
                'credit' => $payCreditHistory
            ]);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
        }
    }

    /**
     * @return \Closure|mixed|object|void|null
     * @throws \Illuminate\Contracts\Container\BindingResolutionException
     */
    public function getAdapter()
    {
        if ($this->campaign->send_type == 'sms') {
            return app()->makeWith(SmsNotification::class, ['campaign' => $this->campaign]);
        } elseif ($this->campaign->send_type == 'email') {
            return app()->makeWith(EmailNotification::class, ['campaign' => $this->campaign]);
        } elseif ($this->campaign->send_type == 'telegram') {
            return app()->makeWith(TelegramNotification::class, ['campaign' => $this->campaign]);
        } elseif ($this->campaign->send_type == 'viber') {
            return app()->makeWith(ViberNotification::class, ['campaign' => $this->campaign]);
        }
    }
}


