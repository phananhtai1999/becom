<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SectionTemplate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class SectionTemplateQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return SectionTemplate::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new SectionTemplate())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'title',
                'template',
                'template_json',
                'user_uuid',
                'section_category_uuid',
                'publish_status',
                'is_default',
                'reject_reason',
                'type',
                'html_template',
                'css_template',
                'js_template',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'title',
                'template',
                'template_json',
                'user_uuid',
                'section_category_uuid',
                'publish_status',
                'is_default',
                'reject_reason',
                'type',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'title',
                AllowedFilter::exact('exact__title', 'title'),
                'template_json',
                AllowedFilter::exact('exact__template_json', 'template_json'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'section_category_uuid',
                AllowedFilter::exact('exact__section_category_uuid', 'section_category_uuid'),
                'sectionCategory.title',
                AllowedFilter::scope('exact__sectionCategory.title', 'sectionCategoryTitle'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'is_default',
                AllowedFilter::exact('exact__is_default', 'is_default'),
                'reject_reason',
                AllowedFilter::exact('exact__reject_reason', 'reject_reason'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'html_template',
                AllowedFilter::exact('exact__html_template', 'html_template'),
                'css_template',
                AllowedFilter::exact('exact__css_template', 'css_template'),
                'js_template',
                AllowedFilter::exact('exact__js_template', 'js_template'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return SectionTemplate::class;
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
