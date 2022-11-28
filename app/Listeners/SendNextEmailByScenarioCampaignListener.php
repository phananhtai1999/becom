<?php

namespace App\Listeners;

use App\Events\SendNextEmailByScenarioCampaignEvent;
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
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SendNextEmailByScenarioCampaignListener implements ShouldQueue
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
     * @param SendNextEmailByScenarioCampaignEvent $event
     * @return void
     */
    public function handle(SendNextEmailByScenarioCampaignEvent $event)
    {
        $campaign = $event->campaignScenario;
        $contact = $event->contact;

        $user = $campaign->user;
        $config = $this->configService->findConfigByKey('smtp_auto');
        $creditNumberSendEmail = config('credit.default_credit');

        if (!$this->userService->checkCreditToSendCEmail($creditNumberSendEmail, $campaign->user_uuid)){
            $this->campaignService->update($campaign, [
                'was_stopped_by_owner' => true
            ]);
        }else {
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
                    'credit' => $creditNumberSendEmail,
                    'type' => $campaign->send_type
                ]);
                DB::commit();
            }catch (\Exception $e) {
                DB::rollback();
            }

            $mailTemplate = $this->mailTemplateVariableService->renderBody($campaign->mailTemplate, $contact, $campaign->smtpAccount, $campaign);

            $mailSendingHistory = $this->mailSendingHistoryService->create([
                'email' => $contact->email,
                'campaign_uuid' => $campaign->uuid,
                'time' => Carbon::now()
            ]);

            $emailTracking = $this->mailTemplateVariableService->injectTrackingImage($mailTemplate, $mailSendingHistory->uuid);
            try {
                Mail::to($contact->email)->send(new SendCampaign($emailTracking));
            } catch (\Exception $e) {
                $this->mailSuccess = false;
                $this->creditReturn = config('credit.default_credit');
                $this->mailSendingHistoryService->update($mailSendingHistory, [
                    'status' => 'fail'
                ]);
                $this->sendEmailScheduleLogService->update($sendEmailScheduleLog, [
                    'is_running' => false,
                    'was_crashed' => true,
                    'log' => $e->getMessage()
                ]);
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
