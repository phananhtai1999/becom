<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\WebsitePage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyWebsitePageQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return WebsitePage::where('user_uuid', auth()->user()->getKey());
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new WebsitePage())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'title',
                'template',
                'template_json',
                'user_uuid',
                'website_page_category_uuid',
                'publish_status',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'title',
                'template',
                'template_json',
                'user_uuid',
                'website_page_category_uuid',
                'publish_status',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'title',
                AllowedFilter::exact('exact__title', 'title'),
                'template',
                AllowedFilter::exact('exact__template', 'template'),
                'template_json',
                AllowedFilter::exact('exact__template_json', 'template_json'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'website_page_category_uuid',
                AllowedFilter::exact('exact__website_page_category_uuid', 'website_page_category_uuid'),
                'websitePageCategory.title',
                AllowedFilter::exact('exact__websitePageCategory.name', 'websitePageCategory.title'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return WebsitePage::class;
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
