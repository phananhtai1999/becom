<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Contact;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyContactQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Contact::where([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()]
        ]);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Contact())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'email',
                'first_name',
                'last_name',
                'middle_name',
                'points',
                'phone',
                'sex',
                'dob',
                'city',
                'country',
                'avatar',
                'status_uuid',
                'user_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'email',
                'first_name',
                'last_name',
                'middle_name',
                'points',
                'phone',
                'sex',
                'dob',
                'city',
                'country',
                'avatar',
                'status_uuid',
                'user_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'middle_name',
                AllowedFilter::exact('exact__middle_name', 'middle_name'),
                'points',
                AllowedFilter::exact('exact__points', 'points'),
                'phone',
                AllowedFilter::exact('exact__phone', 'phone'),
                'sex',
                AllowedFilter::exact('exact__sex', 'sex'),
                'dob',
                AllowedFilter::exact('exact__dob', 'dob'),
                'city',
                AllowedFilter::exact('exact__city', 'city'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'avatar',
                AllowedFilter::exact('exact__avatar', 'avatar'),
                'status_uuid',
                AllowedFilter::exact('exact_status_uuid', 'status_uuid'),
                'status.name',
                AllowedFilter::exact('exact__status.name', 'status.name'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                AllowedFilter::scope('uuids_not_in'),
                AllowedFilter::scope('uuids_in'),
                AllowedFilter::scope('from__dob'),
                AllowedFilter::scope('to__dob'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Contact::class;
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
