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
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function customMyFilterSendTypeOnCampaign($filters, $fieldSort, $orderBy, $perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        if (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'added') {

            return MyAddCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        } elseif (!empty($filters['credit_transaction_history']) && $filters['credit_transaction_history'] == 'used') {

            return MyCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        }

        return $this->myFilterAddAndUseCreditTransactionHistory($fieldSort, $orderBy, $perPage, $columns, $pageName, $page, $search, $searchBy);
    }

    /**
     * @param $fieldSort
     * @param $orderBy
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public function myFilterAddAndUseCreditTransactionHistory($fieldSort, $orderBy, $perPage, $columns, $pageName, $page, $search, $searchBy)
    {
        $myAddCreditTransactionHistory = MyAddCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy);

        return MyCreditTransactionHistoryQueryBuilder::searchQuery($search, $searchBy)->unionAll($myAddCreditTransactionHistory)->orderBy(ltrim($fieldSort, '-'), $orderBy)->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }
}
