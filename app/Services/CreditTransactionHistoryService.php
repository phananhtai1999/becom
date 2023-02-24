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
     * @param $orderBy
     * @param $countFilters
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function customFilterSendTypeOnCampaign($filters, $fieldSort, $orderBy, $countFilters, $perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        if ($countFilters == 1) {
            $models = $this->model
                ->where('credit', '!=', '0')
                ->whereNull('campaign_uuid');

            return CreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy)->unionAll($models)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'added') {

            return AddCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'used') {

            return UseCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        }
        $addCreditTransactionHistory = AddCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy);

        return UseCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy)->unionAll($addCreditTransactionHistory)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }
}
