<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\CreditPackageHistory;
use App\Models\PlatformPackage;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class CreditPackageHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return CreditPackageHistory::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new CreditPackageHistory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'user_uuid',
                'credit_package_uuid',
                'payment_method_uuid',
                'logs',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'credit_package_uuid',
                'payment_method_uuid',
                'logs',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'credit_package_uuid',
                AllowedFilter::exact('exact__credit_package_uuid', 'credit_package_uuid'),
                'payment_method_uuid',
                AllowedFilter::exact('exact__payment_method_uuid', 'payment_method_uuid'),
                'logs',
                AllowedFilter::exact('exact__logs', 'logs'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return CreditPackageHistory::class;
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
