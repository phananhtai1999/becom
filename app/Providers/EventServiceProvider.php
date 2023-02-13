<?php

namespace App\Providers;

use App\Events\PaymentSuccessfullyEvent;
use App\Events\SendByBirthdayCampaignEvent;
use App\Events\SendByCampaginRootScenarioEvent;
use App\Events\SendByCampaignEvent;
use App\Events\SendNotOpenByScenarioCampaignEvent;
use App\Events\SendEmailRecoveryPasswordEvent;
use App\Events\SendEmailVerifyEmailEvent;
use App\Events\SendNextByScenarioCampaignEvent;
use App\Listeners\PaymentSuccessfullyListener;
use App\Listeners\SendByBirthdayCampaignListener;
use App\Listeners\SendByCampaginRootScenarioListener;
use App\Listeners\SendByCampaignListener;
use App\Listeners\SendNotOpenByScenarioCampaignListener;
use App\Listeners\SendEmailRecoveryPasswordListener;
use App\Listeners\SendEmailVerifyEmailListener;
use App\Listeners\SendNextByScenarioCampaignListener;
use App\Models\Config;
use App\Models\Contact;
use App\Models\Role;
use App\Models\User;
use App\Models\UserAccessToken;
use App\Models\UserConfig;
use App\Models\UserDetail;
use App\Observers\ConfigObserver;
use App\Observers\ContactObserver;
use App\Observers\RoleObserver;
use App\Observers\UserAccessTokenObserver;
use App\Observers\UserConfigObserver;
use App\Observers\UserDetailObserver;
use App\Observers\UserObserver;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        SendEmailRecoveryPasswordEvent::class => [
            SendEmailRecoveryPasswordListener::class
        ],
        SendEmailVerifyEmailEvent::class => [
            SendEmailVerifyEmailListener::class
        ],
        SendByCampaignEvent::class => [
            SendByCampaignListener::class
        ],
        SendByBirthdayCampaignEvent::class => [
            SendByBirthdayCampaignListener::class
        ],
        SendNextByScenarioCampaignEvent::class => [
            SendNextByScenarioCampaignListener::class
        ],
        SendNotOpenByScenarioCampaignEvent::class => [
            SendNotOpenByScenarioCampaignListener::class
        ],
        PaymentSuccessfullyEvent::class => [
            PaymentSuccessfullyListener::class
        ],
        SendByCampaginRootScenarioEvent::class => [
            SendByCampaginRootScenarioListener::class
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        User::observe(UserObserver::class);
        UserDetail::observe(UserDetailObserver::class);
        UserConfig::observe(UserConfigObserver::class);
        UserAccessToken::observe(UserAccessTokenObserver::class);
        Role::observe(RoleObserver::class);
        Config::observe(ConfigObserver::class);
        Contact::observe(ContactObserver::class);
    }
}
