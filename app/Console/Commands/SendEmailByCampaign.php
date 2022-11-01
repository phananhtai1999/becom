<?php

namespace App\Console\Commands;

use App\Events\SendEmailByCampaignEvent;
use App\Services\CampaignService;
use App\Services\ContactService;
use App\Services\EmailService;
use App\Services\MailSendingHistoryService;
use App\Services\SendEmailByCampaignService;
use Illuminate\Console\Command;

class SendEmailByCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email by campaign';

    /**
     * @var CampaignService
     */
    protected $service;

    /**
     * @var ContactService
     */
    protected $contactService;

    /**
     * @var SendEmailByCampaignService
     */
    protected $sendEmailByCampaignService;

    /**
     * Create a new command instance.
     *
     *
     * @param CampaignService $service
     * @param ContactService $contactService
     * @param SendEmailByCampaignService $sendEmailByCampaignService
     */
    public function __construct(
        CampaignService $service,
        ContactService $contactService,
        SendEmailByCampaignService $sendEmailByCampaignService
    )
    {
        $this->service = $service;
        $this->sendEmailByCampaignService = $sendEmailByCampaignService;
        $this->contactService = $contactService;
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $activeCampaign = $this->service->loadActiveCampaign();
        $contactsNumberSendEmail = count($this->contactService->getContactsSendEmail($activeCampaign->uuid));
        $creditNumberSendEmail = $contactsNumberSendEmail * config('credit.default_credit') * $activeCampaign->number_email_per_date;
        SendEmailByCampaignEvent::dispatch($activeCampaign, $creditNumberSendEmail);
    }
}
