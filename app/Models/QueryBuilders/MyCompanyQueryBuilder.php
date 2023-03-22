<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Company;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyCompanyQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Company::where(function ($query) {
            $query->where('user_uuid', auth()->user()->getkey())
                ->orWhereNull('user_uuid');
        });
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Company())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'user_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'user_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
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
        return Company::class;
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
