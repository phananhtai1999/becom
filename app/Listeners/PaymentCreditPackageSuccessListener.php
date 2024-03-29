<?php

namespace App\Listeners;

use App\Models\CreditPackageHistory;
use App\Models\Invoice;
use App\Services\CreditPackageService;
use App\Services\UserCreditHistoryService;
use App\Services\UserProfileService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentCreditPackageSuccessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(
        UserCreditHistoryService $userCreditHistoryService,
        UserProfileService $userProfileService
    )
    {
        $this->userProfileService = $userProfileService;
        $this->userCreditHistoryService = $userCreditHistoryService;
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        DB::beginTransaction();
        try {
            $creditPackageHistory = CreditPackageHistory::create([
                'credit_package_uuid' => $event->creditPackageUuid,
                'user_uuid' => $event->userUuid,
                'app_id' => auth()->appId(),
                'logs' => json_encode($event->paymentData),
                'payment_method_uuid' => $event->paymentMethodUuid,
                'invoice_uuid' => $event->invoice->uuid
            ]);
            $model = $this->userCreditHistoryService->create([
                'user_uuid' => $event->userUuid,
                'app_id' => auth()->appId(),
                'credit' => $creditPackageHistory->creditPackage->credit,
                'add_by_uuid' => $event->userUuid,
            ]);
            $this->userProfileService->update($model->user, ['credit' => $model->user->credit + $model->credit]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return throw new \Exception($exception->getMessage(), 400);
        }
    }
}
