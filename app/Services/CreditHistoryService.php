<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\CreditHistoryQueryBuilder;
use App\Models\CreditHistory;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class CreditHistoryService extends AbstractService
{
    protected $modelClass = CreditHistory::class;

    protected $modelQueryBuilderClass = CreditHistoryQueryBuilder::class;

    /**
     * @return SortsQuery|QueryBuilder
     */
    public function userUseCreditHistories()
    {
        return QueryBuilder::for($this->model)
            ->defaultSort('-created_at')
            ->allowedFilters([
                AllowedFilter::exact('uuid'),
                AllowedFilter::exact('user_uuid'),
                AllowedFilter::exact('credit'),
                AllowedFilter::exact('campaign_uuid'),
                AllowedFilter::exact('add_by_uuid'),

            ])
            ->select('uuid', 'user_uuid', 'credit', 'campaign_uuid', DB::raw('NULL as campaign_uuid'), 'created_at');
    }
}
