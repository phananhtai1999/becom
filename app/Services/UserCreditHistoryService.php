<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserCreditHistoryQueryBuilder;
use App\Models\UserCreditHistory;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;

class UserCreditHistoryService extends AbstractService
{
    protected $modelClass = UserCreditHistory::class;

    protected $modelQueryBuilderClass = UserCreditHistoryQueryBuilder::class;

    /**
     * @param $model
     * @param $credit
     * @param $userCredit
     * @return mixed
     */
    public function updateUserCredit($model, $credit, $userCredit)
    {
        return $model->update([
            'credit' => $credit + $userCredit
        ]);
    }

    /**
     * @param $data
     * @return \Spatie\QueryBuilder\Concerns\SortsQuery|QueryBuilder
     */
    public function userAddCreditHistories($data)
    {
        return QueryBuilder::for($this->model)
            ->select('uuid', 'user_uuid', 'credit', DB::raw('NULL as campaign_uuid'), 'add_by_uuid', 'created_at')
            ->unionAll($data)
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::exact('uuid'),
                AllowedFilter::exact('user_uuid'),
                AllowedFilter::exact('credit'),
                AllowedFilter::exact('campaign_uuid'),
                AllowedFilter::exact('add_by_uuid'),
            ]);
    }
}
