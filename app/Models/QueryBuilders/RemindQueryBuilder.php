<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Company;
use App\Models\Remind;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class RemindQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Remind::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Remind())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'note',
                'date',
                'user_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'note',
                'date',
                'user_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'note',
                AllowedFilter::exact('exact__note', 'note'),
                'date',
                AllowedFilter::exact('exact__date', 'date'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Remind::class;
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
