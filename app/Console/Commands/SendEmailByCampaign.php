<?php

namespace App\Console\Commands;

use App\Events\SendEmailByCampaignEvent;
use App\Services\CampaignService;
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
     * @var SendEmailByCampaignService
     */
    protected $sendEmailByCampaignService;

    /**
     * Create a new command instance.
     *
     *
     * @param CampaignService $service
     * @param SendEmailByCampaignService $sendEmailByCampaignService
     */
    public function __construct(
        CampaignService $service,
        SendEmailByCampaignService $sendEmailByCampaignService
    )
    {
        $this->service = $service;
        $this->sendEmailByCampaignService = $sendEmailByCampaignService;
        parent::__construct();
    }

    /**
     * @return void
     */
    public function handle()
    {
        $activeCampaign = $this->service->loadActiveCampaign();
        $toEmails = null;
        SendEmailByCampaignEvent::dispatch($activeCampaign, $toEmails);
    }
}
