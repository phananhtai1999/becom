<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\ActivityHistory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\UserTracking;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class UserTrackingQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return UserTracking::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new UserTracking())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'ip',
                'user_uud',
                'country',
                'postal_code',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'ip',
                'user_uud',
                'country',
                'postal_code',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'ip',
                AllowedFilter::exact('exact__ip', 'ip'),
                'user_uud',
                AllowedFilter::exact('exact__user_uud', 'user_uud'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'postal_code',
                AllowedFilter::exact('exact__postal_code', 'postal_code'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return UserTracking::class;
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
