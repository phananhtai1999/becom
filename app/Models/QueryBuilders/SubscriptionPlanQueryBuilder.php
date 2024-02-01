<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Campaign;
use App\Models\CreditPackage;
use App\Models\App;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SubscriptionPlan;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class SubscriptionPlanQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return SubscriptionPlan::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new SubscriptionPlan())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'app_uuid',
                'duration',
                'duration_type',
                'payment_plan_id',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'app_uuid',
                'duration',
                'duration_type',
                'payment_plan_id',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'app_uuid',
                AllowedFilter::exact('exact__app_uuid', 'app_uuid'),
                'duration',
                AllowedFilter::exact('exact__duration', 'duration'),
                'duration_type',
                AllowedFilter::exact('exact__duration_type', 'duration_type'),
                'payment_plan_id',
                AllowedFilter::exact('exact__payment_plan_id', 'payment_plan_id'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return SubscriptionPlan::class;
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
