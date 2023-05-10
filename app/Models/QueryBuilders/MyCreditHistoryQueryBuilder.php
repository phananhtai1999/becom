<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\CreditHistory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyCreditHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return CreditHistory::where('user_uuid', auth()->user()->getkey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new CreditHistory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'user_uuid',
                'credit',
                'campaign_uuid',
                'scenario_uuid',
                'type'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'credit',
                'campaign_uuid',
                'scenario_uuid',
                'type'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'credit',
                AllowedFilter::exact('exact__credit', 'credit'),
                'campaign_uuid',
                AllowedFilter::exact('exact__campaign_uuid', 'campaign_uuid'),
                'scenario_uuid',
                AllowedFilter::exact('exact__scenario_uuid', 'scenario_uuid'),
                'scenario.name',
                AllowedFilter::exact('exact__scenario.name', 'scenario.name'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'campaign.send_type',
                AllowedFilter::exact('exact__campaign.send_type', 'campaign.send_type'),
                'campaign.tracking_key',
                AllowedFilter::exact('exact__campaign.tracking_key', 'campaign.tracking_key'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return CreditHistory::class;
    }

    /**
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public static function searchQuery($search, $searchBy)
    {
        $initialQuery = static::initialQuery();
        $baseQuery = static::fillAble();

        return SearchQueryBuilder::search($baseQuery, $initialQuery, $search, $searchBy);
    }
}
