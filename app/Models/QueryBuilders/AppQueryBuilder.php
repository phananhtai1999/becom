<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use App\Models\App;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class AppQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return App::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new App())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'monthly',
                'yearly',
                'payment_product_id',
                'name',
                'description',
                'status'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'monthly',
                'yearly',
                'payment_product_id',
                'name',
                'description',
                'status'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'monthly',
                AllowedFilter::exact('exact__monthly', 'monthly'),
                'yearly',
                AllowedFilter::exact('exact__yearly', 'yearly'),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'description',
                AllowedFilter::exact('exact__description', 'description'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'payment_product_id',
                AllowedFilter::exact('exact__payment_product_id', 'payment_product_id'),
                'permissions.uuid',
                AllowedFilter::exact('exact__permissions.uuid', 'permissions.uuid'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return App::class;
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
