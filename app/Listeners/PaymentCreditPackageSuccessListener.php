<?php

namespace App\Listeners;

use App\Models\CreditPackageHistory;
use App\Services\CreditPackageService;

class PaymentCreditPackageSuccessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        CreditPackageHistory::create([
            'credit_package_uuid' => $event->creditPackageUuid,
            'user_uuid' => $event->userUuid,
            'logs' => json_encode($event->paymentData)
        ]);
    }
}
