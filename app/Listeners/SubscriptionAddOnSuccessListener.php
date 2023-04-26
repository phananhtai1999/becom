<?php

namespace App\Listeners;

use App\Models\AddOnSubscriptionHistory;
use App\Models\Invoice;
use App\Models\UserAddOn;
use Illuminate\Support\Facades\Cache;

class SubscriptionAddOnSuccessListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        try {
            $invoice = Invoice::create($event->invoiceData);
            AddOnSubscriptionHistory::create(array_merge($event->subscriptionHistoryData, ['invoice_uuid' => $invoice->uuid]));
            UserAddOn::create($event->userAddOnData);
        } catch (\Exception $exception) {
            dd($exception->getMessage());
        }

        Cache::flush();
    }
}
