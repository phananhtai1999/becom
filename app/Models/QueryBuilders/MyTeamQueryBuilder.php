<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\Team;
use Doctrine\DBAL\Query\QueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;

class MyTeamQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Team::where('owner_uuid', auth()->user()->getkey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Team())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'owner_uuid',
                'leader_uuid',
                'parent_team_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'owner_uuid',
                AllowedFilter::exact('exact__owner_uuid', 'owner_uuid'),
                'owner.email',
                AllowedFilter::exact('exact__owner.email', 'owner.email'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'leader_uuid',
                AllowedFilter::exact('exact__leader_uuid', 'leader_uuid'),
                'parent_team_uuid',
                AllowedFilter::exact('exact__parent_team_uuid', 'parent_team_uuid'),
                AllowedFilter::scope('from__created_at'),
                AllowedFilter::scope('to__created_at'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
                AllowedFilter::scope('from__updated_at'),
                AllowedFilter::scope('to__updated_at'),
                AllowedFilter::scope('team_root'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Team::class;
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
