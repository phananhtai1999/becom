<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserCreditHistoryQueryBuilder;
use App\Models\UserCreditHistory;
use Illuminate\Support\Facades\DB;

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
     * @param $startDate
     * @param $endDate
     * @return \Illuminate\Support\Collection
     */
    public function totalCreditAdded($startDate, $endDate)
    {
        return DB::table('user_credit_histories')->selectRaw('SUM(credit) as sum')
            ->whereDate('created_at', '>=', $startDate)
            ->whereDate('created_at', '<=', $endDate)
            ->whereNull('deleted_at')
            ->get();
    }
}
