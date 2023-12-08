<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Order;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyOrderQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Order::where([
            ['user_uuid', auth()->user()],
            ['app_id', auth()->appId()]
        ]);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Order())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'user_uuid',
                'payment_method_uuid',
                'credit',
                'total_price',
                'status',
                'note',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'payment_method_uuid',
                'credit',
                'total_price',
                'status',
                'note',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'payment_method_uuid',
                AllowedFilter::exact('exact__payment_method_uuid', 'payment_method_uuid'),
                'credit',
                AllowedFilter::exact('exact__credit', 'credit'),
                'total_price',
                AllowedFilter::exact('exact__total_price', 'total_price'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'note',
                AllowedFilter::exact('exact__note', 'note'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Order::class;
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
