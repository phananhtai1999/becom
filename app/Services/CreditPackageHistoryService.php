<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\CreditPackageHistory;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\CreditPackageHistoryQueryBuilder;
use App\Models\QueryBuilders\PermissionQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\SortTotalCreditOfCampaignQueryBuilder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class CreditPackageHistoryService extends AbstractService
{
    protected $modelClass = CreditPackageHistory::class;

    protected $modelQueryBuilderClass = CreditPackageHistoryQueryBuilder::class;
}
