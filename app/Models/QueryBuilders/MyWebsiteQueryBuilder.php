<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\FooterTemplate;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SectionTemplate;
use App\Models\Website;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyWebsiteQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Website::where([
            ['user_uuid', auth()->userId()],
            ['app_id', auth()->appId()]
        ]);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Website())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'title',
                'header_section_uuid',
                'footer_section_uuid',
                'description',
                'user_uuid',
                'publish_status',
                'logo',
                'domain_uuid',
                'tracking_ids',
                'is_active_news_page',
                'is_active_product_page',
                'is_default',
                'menu_properties',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'title',
                'header_section_uuid',
                'footer_section_uuid',
                'description',
                'user_uuid',
                'publish_status',
                'logo',
                'domain_uuid',
                'tracking_ids',
                'is_active_news_page',
                'is_active_product_page',
                'is_default',
                'menu_properties',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'title',
                AllowedFilter::exact('exact__title', 'title'),
                'description',
                AllowedFilter::exact('exact__description', 'description'),
                'logo',
                AllowedFilter::exact('exact__logo', 'logo'),
                'header_section_uuid',
                AllowedFilter::exact('exact__header_section_uuid', 'header_section_uuid'),
                'is_active_news_page',
                AllowedFilter::exact('exact__is_active_news_page', 'is_active_news_page'),
                'is_active_product_page',
                AllowedFilter::exact('exact__is_active_product_page', 'is_active_product_page'),
                'is_default',
                AllowedFilter::exact('exact__is_default', 'is_default'),
                'headerSection.title',
                AllowedFilter::exact('exact__headerSection.title', 'headerSection.title'),
                'footer_section_uuid',
                AllowedFilter::exact('exact__footer_section_uuid', 'footer_section_uuid'),
                'footerSection.title',
                AllowedFilter::exact('exact__footerSection.title', 'footerSection.title'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'domain_uuid',
                AllowedFilter::exact('exact__domain_uuid', 'domain_uuid'),
                'domain.name',
                AllowedFilter::exact('exact__domain.name', 'domain.name'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'tracking_ids',
                AllowedFilter::exact('exact__tracking_ids', 'tracking_ids'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Website::class;
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
