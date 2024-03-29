<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\AddOn;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class AddOnQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return AddOn::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new AddOn())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'description',
                'thumbnail',
                'status',
                'price',
                'app_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'description',
                'thumbnail',
                'status',
                'price',
                'app_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'description',
                AllowedFilter::exact('exact__description', 'description'),
                'thumbnail',
                AllowedFilter::exact('exact__thumbnail', 'thumbnail'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'price',
                AllowedFilter::exact('exact__price', 'price'),
                'app_uuid',
                AllowedFilter::exact('exact__app_uuid', 'app_uuid'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return AddOn::class;
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
