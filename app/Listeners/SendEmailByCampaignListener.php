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
        $config = $this->configService->findConfigByKey('smtp_auto');

        if($user->can_add_smtp_account == 1 || $config->value == 0){
            if(!empty($campaign->smtpAccount)){
                $smtpAccount = $campaign->smtpAccount;
            }else{
                $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
            }
        }else{
            $smtpAccount = $this->smtpAccountService->getRandomSmtpAccountAdmin();
        }
        $this->smtpAccountService->setSmtpAccountForSendEmail($smtpAccount);
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
                'credit' => $creditNumberSendEmail
            ]);
            DB::commit();
        }catch (\Exception $e) {
            DB::rollback();
        }

        $contacts = $this->contactService->getContactsSendEmail($campaign->uuid);

        for ($i = 1; $i <= $campaign->number_email_per_date; $i++) {
            foreach ($contacts as $contact){
                $quantityEmailWasSentPerUser = $this->mailSendingHistoryService->getNumberEmailSentPerUser($campaign->uuid, $contact->email);

                if($quantityEmailWasSentPerUser < $campaign->number_email_per_user){
                    $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $contact, $campaign->smtpAccount, $campaign);

                    $mailSendingHistory = $this->mailSendingHistoryService->create([
                        'email' => $contact->email,
                        'campaign_uuid' =>   $campaign->uuid,
                        'time' => Carbon::now()
                    ]);

                    $emailTracking = $this->mailTemplateVariableService->injectTrackingImage($mailTemplate, $mailSendingHistory->uuid);
                    try {
                        Mail::to($contact->email)->send(new SendCampaign($emailTracking));
                    } catch (\Exception $e) {
                        $this->mailSuccess = false;
                        $this->creditReturn += config('credit.default_credit');
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
            }

            if($this->checkWasFinishedCampaign($campaign)){
                if(($this->numberEmailSentPerDate = $i) < $campaign->number_email_per_date) {
                    $returnUser = ($campaign->number_email_per_date - $this->numberEmailSentPerDate)
                        * config('credit.default_credit') * count($contacts);
                    $payCreditHistory = $this->numberEmailSentPerDate * count($contacts)
                        * config('credit.default_credit');
                    $this->returnCreditUserAndCreditHistory($user, $creditHistory, $returnUser, $payCreditHistory);
                }
                $this->campaignService->update($campaign, ['was_finished' => true]);
                break;
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


//    public function sendEmailByCampaign($campaign, $sendEmailScheduleLog)
//    {
//
//        $contacts = $this->contactService->getContactsSendEmail($campaign->uuid);
//
//        for ($i = 1; $i <= $campaign->number_email_per_date; $i++) {
//            foreach ($contacts as $contact){
//                $quantityEmailWasSentPerUser = $this->mailSendingHistoryService->getNumberEmailSentPerUser($campaign->uuid, $contact->email);
//
//                if($quantityEmailWasSentPerUser < $campaign->number_email_per_user){
//                    $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $contact, $campaign->smtpAccount, $campaign);
//
//                    $mailSendingHistory = $this->mailSendingHistoryService->create([
//                        'email' => $contact->email,
//                        'campaign_uuid' =>   $campaign->uuid,
//                        'time' => Carbon::now()
//                    ]);
//
//                    $emailTracking = $this->mailTemplateVariableService->injectTrackingImage($mailTemplate, $mailSendingHistory->uuid);
//                    try {
//                        Mail::to($contact->email)->send(new SendCampaign($emailTracking));
//                    } catch (\Exception $e) {
//                        $this->mailSuccess = false;
//                        $this->creditReturn += config('credit.default_credit');
//                        $this->mailSendingHistoryService->update($mailSendingHistory, [
//                            'status' => 'fail'
//                        ]);
//                        $this->sendEmailScheduleLogService->update($sendEmailScheduleLog, [
//                            'is_running' => false,
//                            'was_crashed' => true,
//                            'log' => $e->getMessage()
//                        ]);
//                    }
//                }
//            }
//        }
//
//
//
//        if($this->mailSuccess){
//            $this->sendEmailScheduleLogService->update($sendEmailScheduleLog, [
//                'end_time' => Carbon::now(),
//                'is_running' => false
//            ]);
//        }
//
//        if($this->checkWasFinishedCampaign($campaign)){
//            $this->campaignService->update($campaign, ['was_finished' => true]);
//        }
//
//    }

    /**
     * @param $campaign
     * @return bool
     */
    public function checkWasFinishedCampaign($campaign)
    {
        $contacts = $this->contactService->getContactsSendEmail($campaign->uuid);
        foreach ($contacts as $contact){
            if($this->mailSendingHistoryService->getNumberEmailSentPerUser($campaign->uuid, $contact->email) !== $campaign->number_email_per_user){
                return false;
            }
        }
        return true;
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
