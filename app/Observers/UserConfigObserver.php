<?php

namespace App\Observers;

use App\Models\Userconfig;

class UserConfigObserver
{
    /**
     * Handle the Userconfig "created" event.
     *
     * @param  \App\Models\Userconfig  $userconfig
     * @return void
     */
    public function created(Userconfig $userconfig)
    {
        //
    }

    /**
     * Handle the Userconfig "updated" event.
     *
     * @param  \App\Models\Userconfig  $userconfig
     * @return void
     */
    public function updated(Userconfig $userconfig)
    {
        //
    }

    /**
     * Handle the Userconfig "deleted" event.
     *
     * @param  \App\Models\Userconfig  $userconfig
     * @return void
     */
    public function deleted(Userconfig $userconfig)
    {
        //
    }

    /**
     * Handle the Userconfig "restored" event.
     *
     * @param  \App\Models\Userconfig  $userconfig
     * @return void
     */
    public function restored(Userconfig $userconfig)
    {
        //
    }

    /**
     * Handle the Userconfig "force deleted" event.
     *
     * @param  \App\Models\Userconfig  $userconfig
     * @return void
     */
    public function forceDeleted(Userconfig $userconfig)
    {
        //
    }
}
