<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\AddOn;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\ShortCodeGroup;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ShortCodeGroupQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return ShortCodeGroup::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new ShortCodeGroup())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'key',
                'name',
                'description',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'key',
                'name',
                'description',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'key',
                AllowedFilter::exact('exact__key', 'key'),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'description',
                AllowedFilter::exact('exact__description', 'description'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return ShortCodeGroup::class;
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
