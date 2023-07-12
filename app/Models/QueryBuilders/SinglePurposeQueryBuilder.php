<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SinglePurpose;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class SinglePurposeQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return SinglePurpose::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new SinglePurpose())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'slug',
                'parent_uuid',
                'user_uuid',
                'title'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'slug',
                'parent_uuid',
                'user_uuid',
                'title'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'slug',
                AllowedFilter::exact('exact__slug', 'slug'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'parent_uuid',
                AllowedFilter::exact('exact__parent_uuid', 'parent_uuid'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                AllowedFilter::scope('title'),
                AllowedFilter::scope('exact__title','exactTitleCategory'),
                'parentSinglePurpose.title',
                AllowedFilter::scope('exact__parentSinglePurpose.title', 'parentSinglePurposeTitle'),
                'parentSinglePurpose.slug',
                AllowedFilter::exact('exact__parentSinglePurpose.slug', 'parentSinglePurpose.slug'),
                AllowedFilter::scope('category_root'),
                AllowedFilter::scope('from__created_at'),
                AllowedFilter::scope('to__created_at'),
                AllowedFilter::scope('from__updated_at'),
                AllowedFilter::scope('to__updated_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return SinglePurpose::class;
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
