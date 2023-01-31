<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\UserDetail;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class UserDetailQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return UserDetail::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new UserDetail())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'about',
                'gender',
                'date_of_birth',
                'user_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'about',
                'gender',
                'date_of_birth',
                'user_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'about',
                AllowedFilter::exact('exact__about', 'about'),
                'gender',
                AllowedFilter::exact('exact__gender', 'gender'),
                'date_of_birth',
                AllowedFilter::exact('exact__date_of_birth', 'date_of_birth'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                AllowedFilter::scope('from__date_of_birth'),
                AllowedFilter::scope('to__date_of_birth'),
            ]);
    }
}
