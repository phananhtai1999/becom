<?php

namespace App\Listeners;

use App\Models\Invoice;
use App\Models\SubscriptionHistory;
use App\Models\UserApp;
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
        SubscriptionHistory::create(array_merge($event->subscriptionHistory, ['invoice_uuid' => $event->invoice->uuid, 'app_id' => auth()->appId()]));
//        UserApp::where('user_uuid', $event->userUuid)->delete();
        UserApp::create($event->userApp);
        Cache::forget('platform_permission_' . $event->userApp['user_uuid']);
    }
}
