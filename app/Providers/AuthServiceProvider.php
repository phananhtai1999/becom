<?php

namespace App\Providers;

use App\Models\AddOn;
use App\Models\Permission;
use App\Models\App;
use App\Services\UserProfileService;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Guards\TokenGuard;
use App\Services\UserAccessTokenService;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Techup\ApiList\Models\GroupApiList;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Auth::extend('custom-token', function ($app, $name, array $config) {
            return new TokenGuard(
                Auth::createUserProvider($config['provider']),
                app(Request::class),
                app(UserAccessTokenService::class),
                app(UserService::class)
            );
        });

        Gate::define('permission', function ($user, $code) {
            Cache::flush();
            $user = app(UserProfileService::class)->findOneWhereOrFail(['user_uuid' => auth()->userId(), 'app_id' => \auth()->appId()]);
            if (!isset($user->userApp->platform_package_uuid) && !isset($user->userAddOns) && !isset($user->userTeam->permission_uuids)) {
                return false;
            }
            //check team leader
            if (isset($user->userTeam) && $user->userTeam->team->leader_uuid == auth()->userId()) {
                $cacheTeamLeaderAddOns = Cache::rememberForever('team_leader_add_on_permission_' . auth()->userId(), function () use ($user) {
                    $permissions = [];
                    foreach ($user->userTeam->team->addOns as $addOn) {
                        $permissions = array_merge($permissions, $addOn->groupApis()->pluck('code')->toArray() ?? []);
                    }
                    return $permissions;
                });
                if (in_array($code, $cacheTeamLeaderAddOns ?? [])) {
                    return true;
                }

            }
            //check team
            if (isset($user->userTeam->permission_uuids) && !$user->userTeam->is_blocked) {
                $cacheTeams = Cache::rememberForever('team_permission_' . auth()->userId(), function () use ($user) {

                    return GroupApiList::whereIn('uuid', $user->userTeam->permission_uuids)->get();
                });
                if (in_array($code, $cacheTeams->pluck('code')->toArray() ?? [])) {
                    return true;
                }

                //team add on
                $cacheUserTeamAddOns = Cache::rememberForever('team_add_on_permission_' . auth()->userId(), function () use ($user) {
                    $permissions = [];
                    foreach ($user->userTeam->addOns as $userTeamAddOn) {
                        $permissions = array_merge($permissions, $userTeamAddOn->groupApis()->pluck('code')->toArray() ?? []);
                    }
                    return $permissions;
                });
                if (in_array($code, $cacheUserTeamAddOns ?? [])) {
                    return true;
                }
            }
            //check platform
            if (isset($user->userApp->platform_package_uuid)) {
                $permissions = Cache::rememberForever('platform_permission_' . auth()->userId(), function () use ($user) {
                    $platformPackage = App::findOrFail($user->userApp->platform_package_uuid);
                    return $platformPackage->groupApis()->pluck('code')->toArray();
                });
                if (in_array($code, $permissions ?? [])) {
                    return true;
                }
            }
            //check add-on
            if (isset($user->userAddOns)) {
                $cacheAddOns = Cache::rememberForever('add_on_permission_' . auth()->userId(), function () use ($user) {
                    $permissions = [];
                    foreach ($user->userAddOns as $userAddOn) {
                        $permissions = array_merge($permissions, $userAddOn->addOnSubscriptionPlan->addOn->groupApis()->pluck('code')->toArray() ?? []);
                    }

                    return $permissions;
                });
                if (in_array($code, $cacheAddOns ?? [])) {
                    return true;
                }
            }

            return false;
        });
    }
}
