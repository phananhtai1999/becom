<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\CreditTransactionHistoryQueryBuilder;
use App\Models\CreditTransactionHistory;

class CreditTransactionHistoryService extends AbstractService
{
    protected $modelClass = CreditTransactionHistory::class;

    protected $modelQueryBuilderClass = CreditTransactionHistoryQueryBuilder::class;

    /**
     * @param $filters
     * @param $arrayFilters
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return false|\Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function customFilterSendTypeOnCampaign($filters, $arrayFilters, $perPage, $columns, $pageName, $page)
    {
        if (!empty($filters) && (in_array($filters, $arrayFilters))) {
            $models = $this->model->whereNull('campaign_uuid');

            return CreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($models)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        }

        return false;
    }
}
