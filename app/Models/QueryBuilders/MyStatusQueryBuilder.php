<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyStatusQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        $myStatus = Status::where('user_uuid', auth()->user()->getkey());
        if ($myStatus->count() != 0) {

            return $myStatus;
        }

        return Status::where('user_uuid', null);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Status())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'user_uuid',
                'points'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'user_uuid',
                'points'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                AllowedFilter::scope('name'),
                'points',
                AllowedFilter::exact('exact__points', 'points'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'user.roles.name',
                AllowedFilter::exact('exact__user.roles.name', 'user.roles.name'),
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
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
        return Status::class;
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
