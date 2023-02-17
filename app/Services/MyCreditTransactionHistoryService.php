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
     * @param $fieldSort
     * @param $orderBy
     * @param $countFilters
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function customMyFilterSendTypeOnCampaign($filters, $fieldSort, $orderBy, $countFilters, $perPage, $columns, $pageName, $page)
    {
        if ($countFilters == 1) {
            $models = $this->model
                ->where('user_uuid', auth()->user()->getkey())
                ->whereNull('campaign_uuid');

            return MyCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($models)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'added') {

            return MyAddCreditTransactionHistoryQueryBuilder::initialQuery()->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'used') {

            return MyCreditTransactionHistoryQueryBuilder::initialQuery()->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        }

        return$this->myFilterAddAndUseCreditTransactionHistory($fieldSort, $orderBy, $perPage, $columns, $pageName, $page);
    }

    /**
     * @param $fieldSort
     * @param $orderBy
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function myFilterAddAndUseCreditTransactionHistory($fieldSort, $orderBy, $perPage, $columns, $pageName, $page)
    {
        $myAddCreditTransactionHistory = MyAddCreditTransactionHistoryQueryBuilder::initialQuery();

        return MyCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($myAddCreditTransactionHistory)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }
}
