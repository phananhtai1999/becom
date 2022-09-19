<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\UserCreditHistoryQueryBuilder;
use App\Models\UserCreditHistory;

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
}
