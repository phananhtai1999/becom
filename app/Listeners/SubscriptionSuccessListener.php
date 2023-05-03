<?php

namespace App\Listeners;

use App\Models\Invoice;
use App\Models\SubscriptionHistory;
use App\Models\UserPlatformPackage;
use Illuminate\Support\Facades\Cache;

class SubscriptionSuccessListener
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
        SubscriptionHistory::create(array_merge($event->subscriptionHistory, ['invoice_uuid' => $event->invoice->uuid]));
        UserPlatformPackage::where('user_uuid', $event->userUuid)->delete();
        UserPlatformPackage::create($event->userPlatformPackage);
        Cache::forget('platform_permission' . $event->userPlatformPackage['user_uuid']);
    }
}
