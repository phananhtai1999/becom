<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\AddOn;
use App\Models\AddOnSubscriptionPlan;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class AddOnSubscriptionPlanQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return AddOnSubscriptionPlan::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new AddOnSubscriptionPlan())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'platform_package_uuid',
                'duration',
                'duration_type',
                'payment_plan_id',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'platform_package_uuid',
                'duration',
                'duration_type',
                'payment_plan_id',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'add_on_uuid',
                AllowedFilter::exact('exact__add_on_uuid', 'add_on_uuid'),
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
        return AddOnSubscriptionPlan::class;
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
