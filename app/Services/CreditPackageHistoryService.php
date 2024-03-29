<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\CreditPackageHistory;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\SubscriptionHistoryQueryBuilder;
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

    public function myTopUpHistories()
    {

        return $this->model->where([
            'user_uuid' => auth()->userId(),
            'app_id' => auth()->appId(),
        ])->orderBy('uuid', 'DESC')->get();
    }

    /**
     * @return mixed
     */
    public function getCreditPackageHistoryOfCurrentUser()
    {
        return $this->model->where([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
        ])->first();
    }
}
