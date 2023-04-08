<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Article;
use App\Models\ArticleCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ArticleQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Article::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Article())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'image',
                'slug',
                'user_uuid',
                'article_category_uuid',
                'publish_status',
                'title',
                'content'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'image',
                'slug',
                'user_uuid',
                'article_category_uuid',
                'publish_status',
                'title',
                'content'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'content',
                AllowedFilter::exact('exact__content', 'content'),
                'image',
                AllowedFilter::exact('exact__image', 'image'),
                'slug',
                AllowedFilter::exact('exact__slug', 'slug'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'article_category_uuid',
                AllowedFilter::exact('exact__article_category_uuid', 'article_category_uuid'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'updated_at',
                AllowedFilter::exact('exact__updated_at', 'updated_at'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'articleCategory.title',
                AllowedFilter::scope('exact__articleCategory.title', 'articleCategoryTitle'),
                'articleCategory.slug',
                AllowedFilter::exact('exact__articleCategory.slug', 'articleCategory.slug'),
                AllowedFilter::scope('title'),
                AllowedFilter::scope('from__created_at'),
                AllowedFilter::scope('to__created_at'),
                AllowedFilter::scope('from__updated_at'),
                AllowedFilter::scope('to__updated_at'),
                AllowedFilter::scope('title_by_root', 'titleByRoot')
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Article::class;
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
