<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\ArticleCategory;
use App\Models\BusinessCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class BusinessCategoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return BusinessCategory::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new BusinessCategory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'parent_uuid',
                'publish_status',
                'title',
                'created_at',
                'updated_at',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'parent_uuid',
                'publish_status',
                'title',
                'created_at',
                'updated_at',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'parent_uuid',
                AllowedFilter::exact('exact__parent_uuid', 'parent_uuid'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'parentBusinessCategory.title',
                AllowedFilter::scope('exact__parentBusinessCategory.title', 'parentBusinessCategory.title'),
                AllowedFilter::scope('category_root'),
                AllowedFilter::scope('title'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return BusinessCategory::class;
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
