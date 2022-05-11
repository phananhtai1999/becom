<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Config;
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
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'key',
                'value',
                'default_value',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'key',
                AllowedFilter::exact('exact__key', 'key'),
                'value',
                AllowedFilter::exact('exact__value', 'value'),
                'default_value',
                AllowedFilter::exact('exact__default_value', 'default_value'),
            ]);
    }
}