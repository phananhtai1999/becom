<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SendProject;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class SendProjectQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return SendProject::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new SendProject())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'user_uuid',
                'name',
                'description',
                'logo',
                'business_uuid',
                'status',
                'parent_uuid',
                'domain_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'name',
                'description',
                'logo',
                'business_uuid',
                'status',
                'parent_uuid',
                'domain_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'description',
                AllowedFilter::exact('exact__description', 'description'),
                'logo',
                AllowedFilter::exact('exact__logo', 'logo'),
                'business_uuid',
                AllowedFilter::exact('exact__business_uuid', 'business_uuid'),
                'domain_uuid',
                AllowedFilter::exact('exact__domain_uuid', 'domain_uuid'),
                'parent_uuid',
                AllowedFilter::exact('exact__parent_uuid', 'parent_uuid'),
                'status',
                AllowedFilter::exact('exact_status', 'status'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'locations.uuid',
                AllowedFilter::exact('exact__locations.uuid', 'locations.uuid'),
                'departments.uuid',
                AllowedFilter::exact('exact__departments.uuid', 'departments.uuid'),
                'teams.uuid',
                AllowedFilter::exact('exact__teams.uuid', 'teams.uuid'),
                AllowedFilter::scope('domain_is_null'),
                AllowedFilter::scope('send_project_root'),
                AllowedFilter::scope('name_project', 'field'),
                AllowedFilter::scope('exact__name_project', 'exactField'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return SendProject::class;
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
