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
     * @return ArticleQueryBuilder|QueryBuilder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Article())->getKeyName();
        //Exclude value
        $select = array_diff(array_merge(['created_at', 'updated_at', $modelKeyName], (new Article())->getFillable()), request()->get('exclude', []));

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'image',
                'slug',
                'user_uuid',
                'article_category_uuid',
                'publish_status',
                'title',
                'content',
                'video',
                'content_for_user',
                'reject_reason',
                'content_type',
                'single_purpose_uuid',
                'paragraph_type_uuid',
                'keyword',
                'description',
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
                'content',
                'video',
                'content_for_user',
                'reject_reason',
                'content_type',
                'single_purpose_uuid',
                'paragraph_type_uuid',
                'keyword',
                'description',
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
                'content_type',
                AllowedFilter::exact('exact__content_type', 'content_type'),
                'single_purpose_uuid',
                AllowedFilter::exact('exact__single_purpose_uuid', 'single_purpose_uuid'),
                'paragraph_type_uuid',
                AllowedFilter::exact('exact__paragraph_type_uuid', 'paragraph_type_uuid'),
                'article_category_uuid',
                AllowedFilter::exact('exact__article_category_uuid', 'article_category_uuid'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'video',
                AllowedFilter::exact('exact__video', 'video'),
                'content_for_user',
                AllowedFilter::exact('exact__content_for_user', 'content_for_user'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
                'reject_reason',
                AllowedFilter::exact('exact__reject_reason', 'reject_reason'),
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
                AllowedFilter::scope('title_by_root', 'titleByRoot'),
                AllowedFilter::scope('keyword'),
                AllowedFilter::scope('description'),
            ])->select($select);
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
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function searchQuery($search, $searchBy)
    {
        $initialQuery = static::initialQuery();
        $baseQuery = static::fillAble();

        return SearchQueryBuilder::search($baseQuery, $initialQuery, $search, $searchBy);
    }
}
