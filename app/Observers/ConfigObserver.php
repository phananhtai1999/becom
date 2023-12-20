<?php

namespace App\Observers;

use Techup\ApiConfig\Models\Config;
use Illuminate\Support\Facades\Cache;

class ConfigObserver
{
    /**
     * Handle the Config "created" event.
     *
     * @param  \App\Models\Config  $config
     * @return void
     */
    public function created(Config $config)
    {
        Cache::forget('config');
    }

    /**
     * Handle the Config "updated" event.
     *
     * @param  \App\Models\Config  $config
     * @return void
     */
    public function updated(Config $config)
    {
        Cache::forget('config');
    }

    /**
     * Handle the Config "deleted" event.
     *
     * @param  \App\Models\Config  $config
     * @return void
     */
    public function deleted(Config $config)
    {
        //
    }

    /**
     * Handle the Config "restored" event.
     *
     * @param  \App\Models\Config  $config
     * @return void
     */
    public function restored(Config $config)
    {
        //
    }

    /**
     * Handle the Config "force deleted" event.
     *
     * @param  \App\Models\Config  $config
     * @return void
     */
    public function forceDeleted(Config $config)
    {
        //
    }
}
