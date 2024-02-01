<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\CreditPackageHistory;
use App\Models\App;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SubscriptionHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class SubscriptionHistoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return SubscriptionHistory::class;
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
                'subscription_plan_uuid',
                'subscription_date',
                'expiration_date',
                'status',
                'logs',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'subscription_plan_uuid',
                'subscription_date',
                'expiration_date',
                'status',
                'logs',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'subscription_plan_uuid',
                AllowedFilter::exact('exact__subscription_plan_uuid', 'subscription_plan_uuid'),
                'subscription_date',
                AllowedFilter::exact('exact__subscription_date', 'subscription_date'),
                'expiration_date',
                AllowedFilter::exact('exact__expiration_date', 'expiration_date'),
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
        return SubscriptionHistory::class;
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
