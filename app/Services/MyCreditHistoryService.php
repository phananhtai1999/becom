<?php

namespace App\Services;

use App\Abstracts\AbstractService;
use App\Models\QueryBuilders\MyCreditHistoryQueryBuilder;
use App\Models\CreditHistory;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyCreditHistoryService extends AbstractService
{
    protected $modelClass = CreditHistory::class;

    protected $modelQueryBuilderClass = MyCreditHistoryQueryBuilder::class;

    /**
     * @param $id
     * @return mixed
     */
    public function showMyCreditHistory($id)
    {
        return  $this->findOneWhereOrFail([
            ['user_uuid', auth()->user()->getkey()],
            ['uuid', $id]
        ]);
    }

    /**
     * @param $id
     * @return mixed
     */
    public function deleteMyCreditHistory($id)
    {
        $credit_history = $this->showMyCreditHistory($id);

        return $this->destroy($credit_history->getKey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public function useMyCreditHistories()
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
            ->where('user_uuid', auth()->user()->getkey())
            ->select('uuid', 'user_uuid', 'credit', 'campaign_uuid', DB::raw('NULL as campaign_uuid'), 'created_at');
    }
}
