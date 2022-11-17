<?php

namespace App\Console\Commands;

use App\Events\SendEmailByBirthdayCampaignEvent;
use App\Services\CampaignService;
use Illuminate\Console\Command;

class SendEmailByBirthdayCampaign extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'send:birthday-campaign';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send email by birthday campaign';

    /**
     * @var CampaignService
     */
    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(
        CampaignService $service
    )
    {
        $this->service = $service;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $listBirthdayCampaignUuid = $this->service->getListActiveBirthdayCampaignUuid();
        SendEmailByBirthdayCampaignEvent::dispatch($listBirthdayCampaignUuid);
    }
}
