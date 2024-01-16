<?php

namespace App\Listeners;

use App\Abstracts\AbstractRestAPIController;
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
            AddOnSubscriptionHistory::create(array_merge($event->subscriptionHistoryData, ['invoice_uuid' => $event->invoice->uuid, 'app_id' => auth()->appId()]));
            UserAddOn::create(array_merge($event->userAddOnData, ['app_id' => auth()->appId()]));
            Cache::forget('add_on_permission_' . $event->userAddOnData['user_uuid']);
    }
}
