<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CreditTransactionHistory;
use App\Models\QueryBuilders\MyAddCreditTransactionHistoryQueryBuilder;
use App\Models\QueryBuilders\MyCreditTransactionHistoryQueryBuilder;

class MyCreditTransactionHistoryService extends AbstractService
{
    protected $modelClass = CreditTransactionHistory::class;

    protected $modelQueryBuilderClass = MyCreditTransactionHistoryQueryBuilder::class;

    /**
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function customMyFilterSendTypeOnCampaign($perPage, $columns, $pageName, $page)
    {
        $models = $this->model
            ->where('user_uuid', auth()->user()->getkey())
            ->whereNull('campaign_uuid');

        return MyCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($models)->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }

    /**
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myFilterAddAndUseCreditTransactionHistory($perPage, $columns, $pageName, $page)
    {
        $myAddCreditTransactionHistory = MyAddCreditTransactionHistoryQueryBuilder::initialQuery();

        return MyCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($myAddCreditTransactionHistory)->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }
}
