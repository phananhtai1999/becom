<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\Permission;
use App\Models\PlatformPackage;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\CreditPackageQueryBuilder;
use App\Models\QueryBuilders\PermissionQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\SortTotalCreditOfCampaignQueryBuilder;
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
        $permissions = $owner->userPlatformPackage->platformPackage->permissions;
        foreach ($owner->userAddOns as $userAddOn) {
            $permissions = $permissions->merge($userAddOn->addOnSubscriptionPlan->addOn->permissions ?? []);
        }

        return $permissions;
    }
}
