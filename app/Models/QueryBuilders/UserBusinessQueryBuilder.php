<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\UserBusiness;
use App\Models\UserTeam;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class UserBusinessQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return UserBusiness::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new UserBusiness())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'user_uuid',
                'business_uuid',
                'is_blocked',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'user_uuid',
                'business_uuid',
                'is_blocked',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'business_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'is_blocked',
                AllowedFilter::exact('exact__is_blocked', 'is_blocked'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                AllowedFilter::callback("user.full_name", function (Builder $query, $values) {
                    $query->whereHas('user', function ($q) use ($values) {
                        if (is_array($values)) {
                            $q->where(function ($innerQ) use ($values) {
                                foreach ($values as $value) {
                                    $fullName = ltrim($value, ' ');
                                    $innerQ->orWhereRaw("CONCAT(first_name, ' ', last_name) like '%$fullName%'");
                                }
                            });
                        } else {
                            $fullName = ltrim($values, ' ');
                            $q->whereRaw("CONCAT(first_name, ' ', last_name) like '%$fullName%'");
                        }
                    });
                }),
                //Custom filter full_name Append (EXACT)
                AllowedFilter::callback("exact__user.full_name", function (Builder $query, $values) {
                    $query->whereHas('user', function ($q) use ($values) {
                        if (is_array($values)) {
                            $q->where(function ($innerQ) use ($values) {
                                foreach ($values as $value) {
                                    $fullName = ltrim($value, ' ');
                                    $innerQ->orWhereRaw("CONCAT(first_name, ' ', last_name) = '$fullName'");
                                }
                            });
                        } else {
                            $fullName = ltrim($values, ' ');
                            $q->whereRaw("CONCAT(first_name, ' ', last_name) = '$fullName'");
                        }
                    });
                })
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return UserBusiness::class;
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
