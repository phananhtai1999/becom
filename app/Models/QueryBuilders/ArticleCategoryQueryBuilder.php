<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\ArticleCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleCategoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return ArticleCategory::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new ArticleCategory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'image',
                'slug',
                'parent_uuid',
                'user_uuid',
                'publish_status',
                'title'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'image',
                'slug',
                'parent_uuid',
                'user_uuid',
                'publish_status',
                'title'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'image',
                AllowedFilter::exact('exact__image', 'image'),
                'slug',
                AllowedFilter::exact('exact__slug', 'slug'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'parent_uuid',
                AllowedFilter::exact('exact__parent_uuid', 'parent_uuid'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
                'parentArticleCategory.title',
                AllowedFilter::exact('exact__parentArticleCategory.title', 'parentArticleCategory.title'),
                'parentArticleCategory.slug',
                AllowedFilter::exact('exact__parentArticleCategory.slug', 'parentArticleCategory.slug'),
                AllowedFilter::scope('category_root'),
                AllowedFilter::scope('title'),
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
        return ArticleCategory::class;
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
