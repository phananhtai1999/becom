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
     * @param $perPage
     * @param $columns
     * @param $pageName
     * @param $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function customFilterSendTypeOnCampaign($perPage, $columns, $pageName, $page)
    {
        $models = $this->model->whereNull('campaign_uuid');

        return CreditTransactionHistoryQueryBuilder::initialQuery()->unionAll($models)->paginate(
            $perPage,
            $columns,
            $pageName,
            $page
        );
    }
}
