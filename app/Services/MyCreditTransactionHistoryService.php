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
     * @param $filters
     * @param $arrayFilters
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return false|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function customMyFilterSendTypeOnCampaign($filters, $arrayFilters, $perPage, $columns, $pageName, $page)
    {
        if (in_array($filters, $arrayFilters)) {
            $myCreditTransactions = MyCreditTransactionHistoryQueryBuilder::initialQuery()->get()->count();
            if ($myCreditTransactions >= 1) {
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
        }

        return false;
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
