<?php

namespace App\Listeners;

use App\Models\CreditPackageHistory;
use App\Models\Invoice;
use App\Services\CreditPackageService;
use App\Services\UserCreditHistoryService;
use App\Services\UserService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PaymentCreditPackageSuccessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct(UserService $userService, UserCreditHistoryService $userCreditHistoryService)
    {
        $this->userService = $userService;
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
            $invoice = Invoice::create($event->invoiceData);
            $creditPackageHistory = CreditPackageHistory::create([
                'credit_package_uuid' => $event->creditPackageUuid,
                'user_uuid' => $event->userUuid,
                'logs' => json_encode($event->paymentData),
                'payment_method_uuid' => $event->paymentMethodUuid,
                'invoice_uuid' => $invoice->uuid
            ]);
            $model = $this->userCreditHistoryService->create([
                'user_uuid' => $event->userUuid,
                'credit' => $creditPackageHistory->creditPackage->credit,
                'add_by_uuid' => $event->userUuid,
            ]);
            $this->userService->update($model->user, ['credit' => $model->user->credit + $model->credit]);

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            return throw new \Exception($exception->getMessage(), 400);
        }
    }
}
