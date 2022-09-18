<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyUserCreditHistoryQueryBuilder;
use App\Models\UserCreditHistory;

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
        return  $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
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
}
