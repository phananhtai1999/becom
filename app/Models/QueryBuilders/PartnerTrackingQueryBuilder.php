<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\ActivityHistory;
use App\Models\PartnerTracking;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\UserTracking;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class PartnerTrackingQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return PartnerTracking::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new PartnerTracking())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'ip',
                'partner_uuid',
                'country'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'ip',
                'partner_uuid',
                'country'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'ip',
                AllowedFilter::exact('exact__ip', 'ip'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'partner_uuid',
                AllowedFilter::exact('exact__partner_uuid', 'partner_uuid'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return PartnerTracking::class;
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
