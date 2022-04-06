<?php

namespace App\Observers;

use App\Models\UserAccessToken;
use Illuminate\Support\Str;

class UserAccessTokenObserver
{
    /**
     * @param UserAccessToken $model
     */
    public function creating(UserAccessToken $model)
    {
        $model->{$model->getKeyName()} = md5((string)Str::uuid());
    }

    /**
     * Handle the UserAccessToken "created" event.
     *
     * @param UserAccessToken $model
     * @return void
     */
    public function created(UserAccessToken $model)
    {
        //
    }

    /**
     * Handle the UserAccessToken "updated" event.
     *
     * @param UserAccessToken $UserAccessToken
     * @return void
     */
    public function updated(UserAccessToken $UserAccessToken)
    {
        //
    }

    /**
     * Handle the UserAccessToken "deleted" event.
     *
     * @param UserAccessToken $UserAccessToken
     * @return void
     */
    public function deleted(UserAccessToken $UserAccessToken)
    {
        //
    }

    /**
     * Handle the UserAccessToken "restored" event.
     *
     * @param UserAccessToken $UserAccessToken
     * @return void
     */
    public function restored(UserAccessToken $UserAccessToken)
    {
        //
    }

    /**
     * Handle the UserAccessToken "force deleted" event.
     *
     * @param UserAccessToken $UserAccessToken
     * @return void
     */
    public function forceDeleted(UserAccessToken $UserAccessToken)
    {
        //
    }
}
