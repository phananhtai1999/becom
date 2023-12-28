<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Department;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class DepartmentQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Department::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Department())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'business_uuid',
                'location_uuid',
                'user_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'business_uuid',
                'location_uuid',
                'user_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                AllowedFilter::scope('name'),
                AllowedFilter::scope('exact__name','exactName'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'business_uuid',
                AllowedFilter::exact('exact__business_uuid', 'business_uuid'),
                'location_uuid',
                AllowedFilter::exact('exact__location_uuid', 'location_uuid'),
                'teams.uuid',
                AllowedFilter::exact('exact__teams.uuid', 'teams.uuid'),
                'user.roles.name',
                AllowedFilter::exact('exact__user.roles.name', 'user.roles.name'),
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'sendProjects.uuid',
                AllowedFilter::exact('exact__sendProjects.uuid', 'sendProjects.uuid'),
                AllowedFilter::callback("user_uuid", function (Builder $query, $value) {
                    if ($value === 'null') {
                        $query->whereNull('user_uuid');
                    } else {
                        if (is_array($value)) {
                            if (in_array('null', $value)) {
                                $query->where(function ($query) use ($value) {
                                    for ($i = 1; $i <= count($value); $i++) {
                                        if ($value[$i - 1] === 'null') {
                                            $query->orWhereNull('user_uuid');
                                        } else {
                                            $query->orWhere('user_uuid', 'like', '%' . $value[$i - 1] . '%');
                                        }
                                    }
                                });
                            } else {
                                $query->where(function ($query) use ($value) {
                                    for ($i = 1; $i <= count($value); $i++) {
                                        $query->orWhere('user_uuid', 'like', '%' . $value[$i - 1] . '%');
                                    }
                                });
                            }
                        } else {
                            $query->where('user_uuid', 'like', '%' . $value . '%');
                        }
                    }
                })
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Department::class;
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
