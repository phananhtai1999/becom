<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\CreditTransactionHistory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyCreditTransactionHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return CreditTransactionHistory::select('transactions.*')
            ->join('campaigns', 'campaigns.uuid', '=', 'transactions.campaign_uuid')
            ->where([
                ['transactions.user_uuid', auth()->user()->getkey()],
                ['campaigns.user_uuid', auth()->user()->getkey()],
                ['transactions.credit', '!=', '0']
            ]);
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
                'campaign.send_type',
                AllowedFilter::exact('exact__campaign.send_type', 'campaign.send_type'),
                'campaign.tracking_key',
                AllowedFilter::exact('exact__campaign.tracking_key', 'campaign.tracking_key'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'add_by.email',
                AllowedFilter::exact('exact__add_by.email', 'add_by.email'),
                AllowedFilter::scope('add_by_email'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return CreditTransactionHistory::class;
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
