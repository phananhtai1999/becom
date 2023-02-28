<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use App\Models\PlatformPackage;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class PlatformPackageQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return PlatformPackage::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new PlatformPackage())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'monthly',
                'yearly',
                'payment_product_id',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'monthly',
                'yearly',
                'payment_product_id',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'monthly',
                AllowedFilter::exact('exact__monthly', 'monthly'),
                'yearly',
                AllowedFilter::exact('exact__yearly', 'yearly'),
                'payment_product_id',
                AllowedFilter::exact('exact__payment_product_id', 'payment_product_id'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return PlatformPackage::class;
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
