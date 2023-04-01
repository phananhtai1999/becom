<?php

namespace App\Listeners;

use App\Models\AddOnSubscriptionHistory;
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
        AddOnSubscriptionHistory::create($event->subscriptionHistoryData);
        UserAddOn::create($event->userAddOnData);
        Cache::flush();
    }
}
