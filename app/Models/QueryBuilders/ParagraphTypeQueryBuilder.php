<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\ParagraphType;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ParagraphTypeQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return ParagraphType::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new ParagraphType())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'slug',
                'parent_uuid',
                'user_uuid',
                'title',
                'sort',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'slug',
                'parent_uuid',
                'user_uuid',
                'title',
                'sort',
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
                'sort',
                AllowedFilter::exact('exact__sort', 'sort'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                AllowedFilter::scope('title'),
                AllowedFilter::scope('exact__title','exactTitleCategory'),
                'parentParagraphType.title',
                AllowedFilter::scope('exact__parentParagraphType.title', 'parentParagraphTypeTitle'),
                'parentParagraphType.slug',
                AllowedFilter::exact('exact__parentParagraphType.slug', 'parentParagraphType.slug'),
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
        return ParagraphType::class;
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
