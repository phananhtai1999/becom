<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use Techup\ApiConfig\Models\Config;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ConfigQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Config::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Config())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'key',
                'value',
                'default_value',
                'group_id',
                'type',
                'status'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'key',
                'value',
                'default_value',
                'group_id',
                'type',
                'status'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'key',
                AllowedFilter::exact('exact__key', 'key'),
                'value',
                AllowedFilter::exact('exact__value', 'value'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'default_value',
                AllowedFilter::exact('exact__default_value', 'default_value'),
                'group_id',
                AllowedFilter::exact('exact__group_id', 'group_id'),
                'group.name',
                AllowedFilter::exact('exact__group.name', 'group.name'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Config::class;
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
