<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Scenario;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyScenarioQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Scenario::where('user_uuid', auth()->user()->getKey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Scenario())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'user_uuid',
                'status',
                'last_stopped_at'
            ])
            ->defaultSort('-created_at')
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'last_stopped_at',
                AllowedFilter::exact('exact__last_stopped_at', 'last_stopped_at'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                AllowedFilter::scope('from__created_at'),
                AllowedFilter::scope('to__created_at'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
                AllowedFilter::scope('from__updated_at'),
                AllowedFilter::scope('to__updated_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Scenario::class;
    }

    /**
     * @param $search
     * @param $searchBy
     * @return mixed
     */
    public static function searchQuery($search, $searchBy)
    {
        $initialQuery = static::initialQuery();
        $mutatedAttributes = (new Scenario())->getMutatedAttributes();
        $sort = ltrim(\request()->get('sort'), '-');
        if (!in_array($sort, $mutatedAttributes)){
            $initialQuery = static::initialQuery()->allowedSorts([
                'uuid',
                'name',
                'user_uuid',
                'status',
                'last_stopped_at'
            ]);
        }
        $baseQuery = static::fillAble();

        return SearchQueryBuilder::search($baseQuery, $initialQuery, $search, $searchBy);
    }
}
