<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\CreditPackageQueryBuilder;
use App\Models\QueryBuilders\PermissionQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\SortTotalCreditOfCampaignQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CreditPackageService extends AbstractService
{
    protected $modelClass = CreditPackage::class;

    protected $modelQueryBuilderClass = CreditPackageQueryBuilder::class;
}
