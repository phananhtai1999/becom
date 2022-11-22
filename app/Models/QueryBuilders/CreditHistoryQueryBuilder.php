<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\CreditHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class CreditHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return CreditHistory::class;
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
                'campaign_uuid',
                'credit',
                'type'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'campaign_uuid',
                'credit',
                'type'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'campaign_uuid',
                AllowedFilter::exact('exact__campaign_uuid', 'campaign_uuid'),
                'credit',
                AllowedFilter::exact('exact__credit', 'credit'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'campaign.send_type',
                AllowedFilter::exact('exact__campaign.send_type', 'campaign.send_type'),
            ]);
    }
}
