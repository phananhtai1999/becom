<?php

namespace App\Console\Commands;

use App\Models\MailSendingHistory;
use Illuminate\Console\Command;
use Techup\Connector\Facades\Connector;
use App\Services\SendEmailScheduleLogService;
use Carbon\Carbon;
use App\Services\CreditHistoryService;
use App\Services\CampaignService;
use App\Notifications\BaseNotification;
class UpdateReceivers extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:recervers {--getall}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update mail sending status by connector';

    /**
     * @var SendEmailScheduleLogService
     */
    protected $sendEmailScheduleLogService;

    /**
     * @var CreditHistoryService
     */
    protected $creditHistoryService;

    /**
     * @var CampaignService
     */
    protected $campaignService;

    /**
     * Maps email status to MailSendingHistory constants
     *
     * @param string $status Status of the email
     * @return Enum Corresponding MailSendingHistory constant or FAIL if no match is found
     */
    
    public function __construct(
        SendEmailScheduleLogService $sendEmailScheduleLogService,
        CreditHistoryService $creditHistoryService,
        CampaignService $campaignService,
    )
    {
        $this->sendEmailScheduleLogService = $sendEmailScheduleLogService;
        $this->creditHistoryService = $creditHistoryService;
        $this->campaignService = $campaignService;
        parent::__construct();
    }
    public function map_status($status) {
        $maps = [
            'error' => MailSendingHistory::FAIL,
            'completed' => MailSendingHistory::SENT,
        ];
        if (isset($maps[$status])) {
            return $maps[$status];
        }
        return MailSendingHistory::FAIL;
    }

    /**
     * @return void
     */
    public function handle() {
        $getall = $this->option('getall');
        if ($getall) {
            $response = Connector::get_all_receivers();
        } else {
            $response = Connector::get_receivers();
        }


        if ($response->successful()) {
            $data = $response->json();
            $reiceivers = $data['data']['processed_receiver'];
            if (is_array($reiceivers)) {
                $reiceivers = collect($reiceivers)->groupBy('status');
                foreach ($reiceivers as $key => $reiceiver) {
                    MailSendingHistory::whereIn('uuid', $reiceiver->pluck('receiver_uuid'))->update(['status' => $this->map_status($key)]);
                }
            }
        }

        $sendEmailScheduleLogs = $this->sendEmailScheduleLogService->findAllWhere(['is_running' => true]);
        foreach($sendEmailScheduleLogs as $sendEmailScheduleLog){
            
            $count = MailSendingHistory::where('campaign_uuid', $sendEmailScheduleLog->campaign_uuid)->where('status', MailSendingHistory::PROCESSING)->count();
            // Processing done
            
            if(!$count){

                $this->sendEmailScheduleLogService->update($sendEmailScheduleLog, [
                    'end_time' => Carbon::now(),
                    'is_running' => false
                ]);
                $creditHistory = $this->creditHistoryService->findOneWhere(['campaign_uuid'=> $sendEmailScheduleLog->campaign_uuid]);
                $campaign = $this->campaignService->findOneById($sendEmailScheduleLog->campaign_uuid);
                if($creditHistory && $campaign){
                    $emailNotification = app()->makeWith(BaseNotification::class, ['campaign' => $campaign])->getAdapter();
                    $configPrice = $emailNotification->getNotificationPrice();
                    $countFail = MailSendingHistory::where('campaign_uuid', $sendEmailScheduleLog->campaign_uuid)->where('status', MailSendingHistory::FAIL)->count();
                    $returnCredit = $configPrice * $countFail;
                    $user = $campaign->user;
                    $emailNotification->returnCreditUserAndCreditHistory($user, $creditHistory, $returnCredit, $creditHistory->credit - $returnCredit);
                }
                
            }
        }

    }
}
