<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Partner;
use App\Models\PartnerCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SectionCategory;
use App\Models\WebsitePage;
use App\Models\WebsitePageCategory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class PartnerQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Partner::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Partner())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'first_name',
                'last_name',
                'company_name',
                'work_email',
                'publish_status',
                'phone_number',
                'answer',
                'partner_category_uuid',
                'user_uuid',
                'partner_level_uuid',
                'code'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'first_name',
                'last_name',
                'company_name',
                'work_email',
                'publish_status',
                'phone_number',
                'answer',
                'partner_category_uuid',
                'user_uuid',
                'partner_level_uuid',
                'code'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'company_name',
                AllowedFilter::exact('exact__company_name', 'company_name'),
                'work_email',
                AllowedFilter::exact('exact__work_email', 'work_email'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'phone_number',
                AllowedFilter::exact('exact__phone_number', 'phone_number'),
                'answer',
                AllowedFilter::exact('exact__answer', 'answer'),
                'partner_category_uuid',
                AllowedFilter::exact('exact__partner_category_uuid', 'partner_category_uuid'),
                'partnerCategory.title',
                AllowedFilter::scope('exact__partnerCategory.title', 'partnerCategoryTitle'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'partner_level_uuid',
                AllowedFilter::exact('exact__partner_level_uuid', 'partner_level_uuid'),
                'partnerLevel.title',
                AllowedFilter::scope('exact__partnerLevel.title', 'partnerLevelTitle'),
                'code',
                AllowedFilter::exact('exact__code', 'code'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Partner::class;
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
