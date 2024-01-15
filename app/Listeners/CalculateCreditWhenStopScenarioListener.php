<?php

namespace App\Listeners;

use App\Events\CalculateCreditWhenStopScenarioEvent;
use App\Services\CampaignScenarioService;
use App\Services\ConfigService;
use App\Services\ContactService;
use App\Services\CreditHistoryService;
use App\Services\MailSendingHistoryService;
use App\Services\UserCreditHistoryService;
use App\Services\UserProfileService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateCreditWhenStopScenarioListener
{
    /**
     * @var MailSendingHistoryService
     */
    private $mailSendingHistoryService;
    /**
     * @var ConfigService
     */
    private $configService;
    /**
     * @var UserCreditHistoryService
     */
    private $userCreditHistoryService;

    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        MailSendingHistoryService $mailSendingHistoryService,
        ConfigService $configService,
        UserCreditHistoryService $userCreditHistoryService,
        UserProfileService $userProfileService

    )
    {
        $this->mailSendingHistoryService = $mailSendingHistoryService;
        $this->configService = $configService;
        $this->userCreditHistoryService = $userCreditHistoryService;
        $this->userProfileService = $userProfileService;
    }

    /**
     * Handle the event.
     *
     * @param  CalculateCreditWhenStopScenarioEvent  $event
     * @return void
     */
    public function handle(CalculateCreditWhenStopScenarioEvent $event)
    {
        $scenario = $event->scenario;
        $listTypeByPrice = $this->configService->getListPriceByType();
        $sumCreditUsed = 0;
        $mailsScenarioGroupByCampaign = $this->mailSendingHistoryService->getMailSendingByScenario($scenario->uuid)->groupBy('campaign_uuid');
        foreach ($mailsScenarioGroupByCampaign as $items) {
            $priceByType = $listTypeByPrice[$items[0]->campaign->send_type]['value'];
            $sumCreditUsed += $items->count() * $priceByType;
        }
        if ($sumCreditUsed > 0) {
            DB::beginTransaction();
            try {
                $creditRefund = $scenario->number_credit - $sumCreditUsed;
                $user = $this->userProfileService->findOneWhere(['user_uuid' => $scenario->user_uuid]);
                $this->userCreditHistoryService->create([
                    'user_uuid' => $scenario->user_uuid,
                    'credit' => $creditRefund,
                    'add_by_uuid' => $scenario->user_uuid
                ]);

                $this->userProfileService->update($user, [
                    'credit' => $user->credit + $creditRefund
                ]);

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                Log::error($exception->getMessage());
            }
        }
    }
}
