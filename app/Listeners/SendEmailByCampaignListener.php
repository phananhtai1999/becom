<?php

namespace App\Listeners;

use App\Events\SendEmailByCampaignEvent;
use App\Mail\SendCampaign;
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
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\DB;
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
     * @var bool
     */
    private $mailSuccess = true;

    /**
     * @var int
     */
    private $numberEmailSentPerDate = 0;

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
     * Handle the event.
     *
     * @param SendEmailByCampaignEvent $event
     * @return void
     * @throws \Throwable
     */
    public function handle(SendEmailByCampaignEvent $event)
    {
        $campaign = $event->campaign;
        $creditNumberSendEmail = $event->creditNumberSendEmail;

        $user = $campaign->user;
        $configSmtpAuto = $this->configService->findConfigByKey('smtp_auto');
        $configEmailPrice = $this->configService->findConfigByKey('email_price');

        if (!$this->userService->checkCreditToSendEmail($creditNumberSendEmail, $campaign->user_uuid)){
            $this->campaignService->update($campaign, [
                'was_stopped_by_owner' => true
            ]);
        }else {
            if($user->can_add_smtp_account == 1 || $configSmtpAuto->value == 0){
                if(!empty($campaign->smtpAccount)){
                    $smtpAccount = $campaign->smtpAccount;
                }else{
                    $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
                }
            }else{
                $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
            }
            $this->smtpAccountService->setSwiftSmtpAccountForSendEmail($smtpAccount);
            $sendEmailScheduleLog = $this->sendEmailScheduleLogService->create([
                'campaign_uuid' => $campaign->getKey(),
                'start_time' => Carbon::now()
            ]);

            DB::beginTransaction();

            try {
                $this->userService->update($user, [
                    'credit' => $user->credit-$creditNumberSendEmail
                ]);

                $creditHistory = $this->creditHistoryService->create([
                    'user_uuid' => $campaign->user_uuid,
                    'campaign_uuid' => $campaign->uuid,
                    'credit' => $creditNumberSendEmail,
                    'type' => $campaign->send_type
                ]);
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
            }

            $contacts = $this->contactService->getContactsSendEmail($campaign->uuid);
            foreach ($contacts as $contact){
                $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $contact, $smtpAccount, $campaign);
                $mailSendingHistory = $this->mailSendingHistoryService->create([
                    'email' => $contact->email,
                    'campaign_uuid' => $campaign->uuid,
                    'time' => Carbon::now()
                ]);

                $emailTracking = $this->mailTemplateVariableService->injectTrackingImage($mailTemplate, $mailSendingHistory->uuid);
                try {
                    Mail::to($contact->email)->send(new SendCampaign($emailTracking, $smtpAccount->mail_from_name, $smtpAccount->mail_from_address));
                } catch (\Exception $e) {
                    $this->mailSuccess = false;
                    $this->creditReturn += $configEmailPrice->value;
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

            if($this->creditReturn > 0) {
                $returnUser = $this->creditReturn;
                $payCreditHistory =  $creditNumberSendEmail - $this->creditReturn;
                $this->returnCreditUserAndCreditHistory($user, $creditHistory, $returnUser, $payCreditHistory);
            }

            if($this->mailSuccess){
                $this->sendEmailScheduleLogService->update($sendEmailScheduleLog, [
                    'end_time' => Carbon::now(),
                    'is_running' => false
                ]);
            }
        }
    }

    /**
     * @param $user
     * @param $creditHistory
     * @param $returnUser
     * @param $payCreditHistory
     * @return void
     */
    public function returnCreditUserAndCreditHistory($user, $creditHistory, $returnUser, $payCreditHistory){
        DB::beginTransaction();

        try {
            $this->userService->update($user, [
                'credit' => $user->credit + $returnUser
            ]);

            $this->creditHistoryService->update($creditHistory, [
                'credit' => $payCreditHistory
            ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
        }
    }

}
