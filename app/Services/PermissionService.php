<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\Permission;
use App\Models\App;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\CreditPackageQueryBuilder;
use App\Models\QueryBuilders\PermissionQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\SortTotalCreditOfCampaignQueryBuilder;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class PermissionService extends AbstractService
{
    protected $modelClass = Permission::class;

    protected $modelQueryBuilderClass = PermissionQueryBuilder::class;

    public function getPermissionForPlatform($permissionAddOnUuid)
    {
        return $this->model->whereNotIn('uuid', $permissionAddOnUuid)->get();
    }

    public function getPermissionOfTeam($owner)
    {
        $permissions = $owner->userApp->platformPackage->permissions;
        foreach ($owner->userAddOns as $userAddOn) {
            $permissions = $permissions->merge($userAddOn->addOnSubscriptionPlan->addOn->permissions ?? []);
        }

        return $permissions;
    }

    public function getPermissionOfUser($userUuid)
    {
        $user = User::find($userUuid);
        $permissions = $user->userApp->platformPackage->permissions ?? [];
        foreach ($user->userAddOns as $userAddOn) {
            $permissions = $permissions->merge($userAddOn->addOnSubscriptionPlan->addOn->permissions ?? []);
        }

        return $permissions;
    }
}
