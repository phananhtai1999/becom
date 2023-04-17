<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\BillingAddress;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;

class BillingAddressQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return BillingAddress::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new BillingAddress())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'user_uuid',
                'email',
                'address',
                'phone',
                'company',
                'country',
                'city',
                'state',
                'zipcode',
                'is_default',
                'type',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'user_uuid',
                'email',
                'address',
                'phone',
                'company',
                'country',
                'city',
                'state',
                'zipcode',
                'is_default',
                'type',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'address',
                AllowedFilter::exact('exact__address', 'address'),
                'phone',
                AllowedFilter::exact('exact__phone', 'phone'),
                'company',
                AllowedFilter::exact('exact__company', 'company'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'city',
                AllowedFilter::exact('exact__city', 'city'),
                'state',
                AllowedFilter::exact('exact__state', 'state'),
                'zipcode',
                AllowedFilter::exact('exact__zipcode', 'zipcode'),
                'is_default',
                AllowedFilter::exact('exact__is_default', 'is_default'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return BillingAddress::class;
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
