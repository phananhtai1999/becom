<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Email;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyEmailQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Email::where([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()]
        ]);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Email())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'email',
                'age',
                'first_name',
                'last_name',
                'country',
                'city',
                'job',
                'user_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'email',
                'age',
                'first_name',
                'last_name',
                'country',
                'city',
                'job',
                'user_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'age',
                AllowedFilter::exact('exact__age', 'age'),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'city',
                AllowedFilter::exact('exact__city', 'city'),
                'job',
                AllowedFilter::exact('exact__job', 'job'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'sendProjects.domain',
                AllowedFilter::exact('exact__sendProjects.domain', 'sendProjects.domain'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Email::class;
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
