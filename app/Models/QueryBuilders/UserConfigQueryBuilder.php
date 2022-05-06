<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\UserConfig;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class UserConfigQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return UserConfig::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new UserConfig())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'app_language',
                'user_language',
                'display_name_style',
                'user_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'app_language',
                'user_language',
                'display_name_style',
                'user_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'app_language',
                AllowedFilter::exact('exact__app_language', 'app_language'),
                'user_language',
                AllowedFilter::exact('exact__user_language', 'user_language'),
                'display_name_style',
                AllowedFilter::exact('exact__display_name_style', 'display_name_style'),

            ]);
    }
}
