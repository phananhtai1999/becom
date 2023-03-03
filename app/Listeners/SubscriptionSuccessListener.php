<?php

namespace App\Listeners;

use App\Models\SubscriptionHistory;
use App\Models\UserPlatformPackage;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        SubscriptionHistory::create($event->subscriptionHistory);
        UserPlatformPackage::create($event->userPlatformPackage);
    }
}
