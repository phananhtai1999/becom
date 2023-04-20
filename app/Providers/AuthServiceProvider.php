<?php

namespace App\Providers;

use App\Models\Permission;
use App\Models\PlatformPackage;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Guards\TokenGuard;
use App\Services\UserAccessTokenService;
use App\Services\UserService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

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
            if (!isset($user->userPlatformPackage->platform_package_uuid) && !isset($user->userAddOns) && !isset($user->userTeam->permission_uuids)) {
                return false;
            }
            //check team
            if (isset($user->userTeam->permission_uuids) && !$user->userTeam->is_blocked) {
                $cacheTeams = Cache::rememberForever('team_permission_' . $user->uuid, function () use ($user) {

                    return Permission::whereIn('uuid', $user->userTeam->permission_uuids)->get();
                });
                foreach ($cacheTeams as $permission) {
                    if (in_array($code, $permission->api_methods ?? [])) {
                        return true;
                    }
                }
            }
            //check platform
            if (isset($user->userPlatformPackage->platform_package_uuid)) {
                $permissions = Cache::rememberForever('platform_permission_' . $user->uuid, function () use ($user) {
                    $platformPackage = PlatformPackage::findOrFail($user->userPlatformPackage->platform_package_uuid);
                    return $platformPackage->permissions()->select('api_methods', 'name', 'code', 'uuid')->get();
                });
                foreach ($permissions as $permission) {
                    if (in_array($code, $permission->api_methods ?? [])) {
                        return true;
                    }
                }
            }
            //check add-on
            if (isset($user->userAddOns)) {
                $cacheAddOns = Cache::rememberForever('add_on_permission_' . $user->uuid, function () use ($user) {
                    $permissions = [];
                    foreach ($user->userAddOns as $userAddOn) {
                        $permissions[] = $userAddOn->addOnSubscriptionPlan->addOn->permissions ?? [];
                    }
                    return $permissions;
                });
                foreach ($cacheAddOns as $permissions) {
                    foreach ($permissions as $permission) {
                        if (in_array($code, $permission->api_methods ?? [])) {
                            return true;
                        }
                    }
                }
            }

            return false;
        });
    }
}
