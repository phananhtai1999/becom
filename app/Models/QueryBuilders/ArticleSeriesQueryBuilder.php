<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\ArticleSeries;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleSeriesQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return ArticleSeries::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new ArticleSeries())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'slug',
                'parent_uuid',
                'title',
                'article_category_uuid',
                'assigned_ids',
                'list_keywords',
                'article_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'slug',
                'parent_uuid',
                'title',
                'article_category_uuid',
                'assigned_ids',
                'list_keywords',
                'article_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'slug',
                AllowedFilter::exact('exact__slug', 'slug'),
                'parent_uuid',
                AllowedFilter::exact('exact__parent_uuid', 'parent_uuid'),
                'article_category_uuid',
                AllowedFilter::exact('exact__article_category_uuid', 'article_category_uuid'),
                'assigned_ids',
                AllowedFilter::exact('exact__assigned_ids', 'assigned_ids'),
                'article_uuid',
                AllowedFilter::exact('exact__article_uuid', 'article_uuid'),
                'list_keywords',
                AllowedFilter::exact('exact__list_keywords', 'list_keywords'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
                AllowedFilter::scope('parentArticleSeries.title', 'parentArticleSeriesTitle'),
                AllowedFilter::scope('exact__parentArticleSeries.title', 'exactParentArticleSeriesTitle'),
                'parentArticleSeries.slug',
                AllowedFilter::exact('exact__parentArticleSeries.slug', 'parentArticleSeries.slug'),
                AllowedFilter::scope('category_root'),
                AllowedFilter::scope('title'),
                AllowedFilter::scope('exact__title','exactTitleCategory'),
                AllowedFilter::scope('from__created_at'),
                AllowedFilter::scope('to__created_at'),
                AllowedFilter::scope('from__updated_at'),
                AllowedFilter::scope('to__updated_at'),
                'article.publish_status',
                AllowedFilter::exact('exact__article.publish_status', 'article.publish_status'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return ArticleSeries::class;
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
