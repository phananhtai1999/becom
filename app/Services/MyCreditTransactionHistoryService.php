<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\CreditTransactionHistory;
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
            $models = $this->model->whereNull('campaign_uuid');

            return MyCreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($models)->paginate(
                $perPage,
                $columns,
                $pageName,
                $page
            );
        }

        return false;
    }
}
