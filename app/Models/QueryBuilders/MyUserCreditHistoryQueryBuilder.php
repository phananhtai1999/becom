<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\UserCreditHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyUserCreditHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return UserCreditHistory::where([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()]
        ]);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new UserCreditHistory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'user_uuid',
                'credit',
                'campaign_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'credit',
                'campaign_uuid',
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
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return UserCreditHistory::class;
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
