<?php

namespace App\Observers;

use App\Models\App;
use App\Models\UserProfile;

class UserProfileObserver
{
    /**
     * Handle the UserProfile "created" event.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return void
     */
    public function created(UserProfile $userProfile)
    {
        $userProfile->userApp()->create(['app_uuid' => App::DEFAULT_PLATFORM_PACKAGE_1, 'app_id' => $userProfile->app_id]);
    }

    /**
     * Handle the UserProfile "updated" event.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return void
     */
    public function updated(UserProfile $userProfile)
    {
        //
    }

    /**
     * Handle the UserProfile "deleted" event.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return void
     */
    public function deleted(UserProfile $userProfile)
    {
        //
    }

    /**
     * Handle the UserProfile "restored" event.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return void
     */
    public function restored(UserProfile $userProfile)
    {
        //
    }

    /**
     * Handle the UserProfile "force deleted" event.
     *
     * @param  \App\Models\UserProfile  $userProfile
     * @return void
     */
    public function forceDeleted(UserProfile $userProfile)
    {
        //
    }
}
