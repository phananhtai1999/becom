<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\AssetGroup;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;

class AssetGroupQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return AssetGroup::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new AssetGroup())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'code',
                'name',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'code',
                'name',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'code',
                AllowedFilter::exact('exact__code', 'code'),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return AssetGroup::class;
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
