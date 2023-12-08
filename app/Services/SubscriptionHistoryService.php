<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\QueryBuilders\CampaignQueryBuilder;
use App\Models\QueryBuilders\SubscriptionHistoryQueryBuilder;
use App\Models\QueryBuilders\PermissionQueryBuilder;
use App\Models\QueryBuilders\SubscriptionPlanQueryBuilder;
use App\Models\QueryBuilders\SortTotalCreditOfCampaignQueryBuilder;
use App\Models\SubscriptionHistory;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class SubscriptionHistoryService extends AbstractService
{
    protected $modelClass = SubscriptionHistory::class;

    protected $modelQueryBuilderClass = SubscriptionHistoryQueryBuilder::class;

    public function mySubscriptionHistories()
    {

        return $this->model->where([
            'user_uuid' => auth()->user(),
            'app_id' => auth()->appId()
        ])->orderBy('uuid', 'DESC')->get();
    }

    public function currentSubscriptionHistory()
    {

        return $this->model->where([
            'user_uuid' => auth()->user(),
            'app_id' => auth()->appId()
        ])->orderBy('uuid', 'DESC')->first();
    }

    public function findByLog($log)
    {
        return $this->model->where('logs->id', $log)->first();
    }

    /**
     * @return mixed
     */
    public function getSubscriptionHistoryOfCurrentUser()
    {
        return $this->model->where([
            ['user_uuid', auth()->user()],
            ['app_id', auth()->appId()],
        ])->first();
    }
}
