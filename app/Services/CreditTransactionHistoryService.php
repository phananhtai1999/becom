<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\AddCreditTransactionHistoryQueryBuilder;
use App\Models\QueryBuilders\CreditTransactionHistoryQueryBuilder;
use App\Models\CreditTransactionHistory;
use App\Models\QueryBuilders\UseCreditTransactionHistoryQueryBuilder;

class CreditTransactionHistoryService extends AbstractService
{
    protected $modelClass = CreditTransactionHistory::class;

    protected $modelQueryBuilderClass = CreditTransactionHistoryQueryBuilder::class;

    /**
     * @param $filters
     * @param $fieldSort
     * @param $countFilters
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function customFilterSendTypeOnCampaign($filters, $fieldSort, $countFilters, $perPage, $columns, $pageName, $page)
    {
        if ($countFilters == 1) {
            $models = $this->model->whereNull('campaign_uuid');

            return CreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($models)->orderByDesc($fieldSort)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'added') {

            return AddCreditTransactionHistoryQueryBuilder::initialQuery()->orderByDesc($fieldSort)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'used') {

            return UseCreditTransactionHistoryQueryBuilder::initialQuery()->orderByDesc($fieldSort)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        }
        $addCreditTransactionHistory = AddCreditTransactionHistoryQueryBuilder::initialQuery();

        return UseCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($addCreditTransactionHistory)->orderByDesc($fieldSort)->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }
}
