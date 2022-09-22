<?php

namespace App\Observers;

use App\Models\User;
use App\Services\UserConfigService;
use App\Services\UserService;

class UserObserver
{
    /**
     * @var UserService
     */
    protected $service;

    /**
     * @param UserService $service
     */
    public function __construct(UserService $service)
    {
        $this->service = $service;
    }

    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        app(UserConfigService::class)->create([
            'user_uuid' => $user->getKey(),
            'app_language' => app()->getLocale(),
            'user_language' => app()->getLocale(),
            'display_name_style' => 1,
        ]);
    }

    /**
     * @param User $user
     * @return void
     */
    public function creating(User $user)
    {
        $user->credit = '0';
    }

    /**
     * Handle the User "updated" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updated(User $user)
    {
        //
    }

    /**
     * @param User $user
     * @return void
     */
    public function updating(User $user)
    {
        //
    }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
