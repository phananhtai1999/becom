<?php

namespace App\Observers;

use App\Models\Userdetail;

class UserDetailObserver
{
    /**
     * Handle the Userdetail "created" event.
     *
     * @param  \App\Models\Userdetail  $userdetail
     * @return void
     */
    public function created(Userdetail $userdetail)
    {
        //
    }

    /**
     * Handle the Userdetail "updated" event.
     *
     * @param  \App\Models\Userdetail  $userdetail
     * @return void
     */
    public function updated(Userdetail $userdetail)
    {
        //
    }

    /**
     * Handle the Userdetail "deleted" event.
     *
     * @param  \App\Models\Userdetail  $userdetail
     * @return void
     */
    public function deleted(Userdetail $userdetail)
    {
        //
    }

    /**
     * Handle the Userdetail "restored" event.
     *
     * @param  \App\Models\Userdetail  $userdetail
     * @return void
     */
    public function restored(Userdetail $userdetail)
    {
        //
    }

    /**
     * Handle the Userdetail "force deleted" event.
     *
     * @param  \App\Models\Userdetail  $userdetail
     * @return void
     */
    public function forceDeleted(Userdetail $userdetail)
    {
        //
    }
}
