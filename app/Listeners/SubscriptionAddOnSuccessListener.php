<?php

namespace App\Listeners;

use App\Models\AddOnHistory;
use App\Models\UserAddOn;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        AddOnHistory::create($event->subscriptionHistoryData);
        UserAddOn::where('user_uuid', $event->userUuid)->delete();
        UserAddOn::create($event->userAddOnData);
    }
}
