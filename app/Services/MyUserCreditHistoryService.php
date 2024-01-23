<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyUserCreditHistoryQueryBuilder;
use App\Models\UserCreditHistory;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class MyUserCreditHistoryService extends AbstractService
{
    protected $modelClass = UserCreditHistory::class;

    protected $modelQueryBuilderClass = MyUserCreditHistoryQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyUserCreditHistory($id)
    {
        return $this->findOneWhereOrFail([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyUserCreditHistory($id)
    {
        $user_credit_history = $this->showMyUserCreditHistory($id);

        return $this->destroy($user_credit_history->getKey());
    }

    /**
     * @param $data
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return LengthAwarePaginator
     */
    public function addMyCreditHistories($data, $perPage, $columns, $pageName, $page)
    {
        return QueryBuilder::for($this->model)
            ->select('uuid', 'user_uuid', 'credit', DB::raw('NULL as campaign_uuid'), 'add_by_uuid', 'created_at')
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
            ->unionAll($data)
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::exact('uuid'),
                AllowedFilter::exact('user_uuid'),
                AllowedFilter::exact('credit'),
                AllowedFilter::exact('campaign_uuid'),
                AllowedFilter::exact('add_by_uuid'),
            ])
            ->paginate($perPage, $columns, $pageName, $page);
    }

    /**
     * @param $startDate
     * @param $endDate
     * @return int
     */
    public function myTotalCreditAdded($startDate, $endDate)
    {
        $totalMyCreditAdded = $this->model->selectRaw('SUM(credit) as sum')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->where([
                ['user_uuid', auth()->userId()],
                ['app_id', auth()->appId()]
            ])
            ->get();

        return !empty($totalMyCreditAdded['0']->sum) ? $totalMyCreditAdded['0']->sum : 0;
    }
}
