<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\CreditTransactionHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class CreditTransactionHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return CreditTransactionHistory::where('credit', '!=', '0');
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new CreditTransactionHistory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'uuid',
                'user_uuid',
                'credit',
                'campaign_uuid',
                'add_by_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'uuid',
                'user_uuid',
                'credit',
                'campaign_uuid',
                'add_by_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'uuid',
                AllowedFilter::exact('exact__uuid', 'uuid'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'credit',
                AllowedFilter::exact('exact__credit', 'credit'),
                'campaign_uuid',
                AllowedFilter::exact('exact__campaign_uuid', 'campaign_uuid'),
                'add_by_uuid',
                AllowedFilter::exact('exact__add_by_uuid', 'add_by_uuid'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'campaign.send_type',
                AllowedFilter::exact('exact__campaign.send_type', 'campaign.send_type'),
                'campaign.tracking_key',
                AllowedFilter::exact('exact__campaign.tracking_key', 'campaign.tracking_key'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'add_by.email',
                AllowedFilter::exact('exact__add_by.email', 'add_by.email'),
            ]);
    }
}
