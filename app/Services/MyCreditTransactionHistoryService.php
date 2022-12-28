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
     * @param $countFilters
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function customMyFilterSendTypeOnCampaign($filters, $countFilters, $perPage, $columns, $pageName, $page)
    {
        if ($countFilters == 1) {
            $models = $this->model
                ->where('user_uuid', auth()->user()->getkey())
                ->whereNull('campaign_uuid');

            return MyCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($models)->orderByDesc('created_at')->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'added') {

            return MyAddCreditTransactionHistoryQueryBuilder::initialQuery()->orderByDesc('created_at')->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'used') {

            return MyCreditTransactionHistoryQueryBuilder::initialQuery()->orderByDesc('created_at')->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        }

        return$this->myFilterAddAndUseCreditTransactionHistory($perPage, $columns, $pageName, $page);
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

        return MyCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($myAddCreditTransactionHistory)->orderByDesc('created_at')->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }
}
