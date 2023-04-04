<?php

namespace App\Notifications;

use App\Events\ActivityHistoryOfSendByCampaignEvent;
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
use Techup\Connector\Facades\Connector;

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

    public function build_sending_config($user)
    {
        $config = $this->getSendOption($user);
        return [
            'smtp_endpoint' => $config->mail_host,
            'smtp_port' => (int)$config->mail_port,
            'smtp_account' => $config->mail_username,
            'smtp_password' => $config->mail_password,
            'from_name' => $config->mail_from_name,
            'from_email' => $config->mail_from_address,
            'smtp_encryption' => $config->smtpAccountEncryption->name,
            'type' => $this->campaign->send_type
        ];
    }

    /**
     * @param $contacts
     * @param $scenario
     * @param $creditTotal
     * @return void
     */
    public function sending_by_conecttor($contacts, $scenario, $creditTotal)
    {

        $user = $this->campaign->user;
        $mailTemplate = $this->campaign->mailTemplate;
        $build_body = collect([]);
        $build_body['config'] = $this->build_sending_config($user);
        $build_body['type'] = $this->campaign->send_type;

        $build_body['subject'] = $this->campaign->mailTemplate->subject;

        $footerTemplateAds = $this->footerTemplateService->getFooterTemplateAdsForSendCampaignByType($mailTemplate->type, $mailTemplate->user);
        $footerTemplateSubscribe = $this->footerTemplateService->getFooterTemplateSubscribeForSendCampaignByType($mailTemplate->type);
        $campaignContent = $this->mailTemplateVariableService->insertFooterTemplateInBodyMailTemplate($mailTemplate->body, optional($footerTemplateAds)->template, optional($footerTemplateSubscribe)->template);
        $build_body['template'] = $campaignContent;

        if (!empty($creditTotal)) {
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
        $build_body['receivers'] = collect([]);
        foreach ($contacts as $contact) {
            $mailSendingHistory = $this->saveMailSendingHistory($contact, $scenario);
            $footerTemplateAds = $this->footerTemplateService->getFooterTemplateAdsForSendCampaignByType($mailTemplate->type, $mailTemplate->user);
            $footerTemplateSubscribe = $this->footerTemplateService->getFooterTemplateSubscribeForSendCampaignByType($mailTemplate->type);
            $reviever = ['uuid' => $mailSendingHistory->uuid];
            $reviever['destination'] = $this->campaign->send_type == 'email' ? $contact->email : $contact->phone;
            $reviever['parameters'] = $this->mapVariablelForSendCampaign($contact, $this->campaign, $mailSendingHistory, $footerTemplateSubscribe);
            $build_body['receivers']->push($reviever);
            //Activity histories
            ActivityHistoryOfSendByCampaignEvent::dispatch($mailSendingHistory, $this->campaign->send_type, $contact->uuid);
        }
        $response = Connector::send_campaign($build_body->toArray());
        if ($response->failed()) {
            return false;
        }
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

        return true;
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
        $mailTemplate = $this->campaign->mailTemplate;
        if (!empty($creditTotal)) {
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

        $footerTemplateAds = $this->footerTemplateService->getFooterTemplateAdsForSendCampaignByType($mailTemplate->type, $mailTemplate->user);
        $footerTemplateSubscribe = $this->footerTemplateService->getFooterTemplateSubscribeForSendCampaignByType($mailTemplate->type);
        $campaignContent = $this->mailTemplateVariableService->insertFooterTemplateInBodyMailTemplate($mailTemplate->body, optional($footerTemplateAds)->template, optional($footerTemplateSubscribe)->template);

        foreach ($contacts as $contact) {
            $mailSendingHistory = $this->saveMailSendingHistory($contact, $scenario);
            $variables = $this->mapVariablelForSendCampaign($contact, $this->campaign, $mailSendingHistory, $footerTemplateSubscribe);
            $emailTracking = $this->getContent($mailTemplate, $campaignContent, $variables);

            try {
                $this->sendContent($contact, $emailTracking, $smtpAccount, $mailTemplate);
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
            //Activity histories
            ActivityHistoryOfSendByCampaignEvent::dispatch($mailSendingHistory, $this->campaign->send_type, $contact->uuid);
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
     * @param $contact
     * @param $campaign
     * @param $mailSendingHistory
     * @param $footerTemplateSubscribe
     * @return array
     */
    public function mapVariablelForSendCampaign($contact, $campaign, $mailSendingHistory, $footerTemplateSubscribe)
    {
        $current = Carbon::now('Asia/Ho_Chi_Minh');
        return [
            'contact_first_name' => $contact->first_name,
            'contact_middle_name' => $contact->middle_name,
            'contact_last_name' => $contact->last_name,
            'contact_phone' => $contact->phone,
            'contact_sex' => $contact->sex,
            'contact_dob' => $contact->dob->toDateString(),
            'contact_country' => $contact->country,
            'contact_city' => $contact->city,
            'website_name' => optional($campaign->website)->name,
            'website_domain' => optional($campaign->website)->domain,
            'website_description' => optional($campaign->website)->description,
            'campaign_from_date' => $campaign->from_date,
            'campaign_to_date' => $campaign->to_date,
            'campaign_tracking_key' => $campaign->tracking_key,
            'current_day' => $current->toDateString(),
            'current_time' => $current->toTimeString(),
            'url_unsubscribe' => $footerTemplateSubscribe ? $this->mailTemplateVariableService->getUrlUnsubscribeByContactUuid($contact->uuid) : null,
            'tracking_pixel_link' => route('mail-open-tracking', $mailSendingHistory->uuid)
        ];
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


