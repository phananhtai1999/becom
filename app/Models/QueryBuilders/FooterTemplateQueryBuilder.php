<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\FooterTemplate;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SectionTemplate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class FooterTemplateQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return FooterTemplate::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new FooterTemplate())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'title',
                'template_type',
                'template',
                'template_json',
                'user_uuid',
                'active_by_uuid',
                'publish_status',
                'is_default',
                'type'
            ])
            ->defaultSort('-is_default')
            ->allowedSorts([
                $modelKeyName,
                'title',
                'template_type',
                'template',
                'template_json',
                'user_uuid',
                'active_by_uuid',
                'publish_status',
                'is_default',
                'type'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'title',
                AllowedFilter::exact('exact__title', 'title'),
                'template_type',
                AllowedFilter::exact('exact__template_type', 'template_type'),
                'template',
                AllowedFilter::exact('exact__template', 'template'),
                'template_json',
                AllowedFilter::exact('exact__template_json', 'template_json'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'active_by_uuid',
                AllowedFilter::exact('exact__active_by_uuid', 'active_by_uuid'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'is_default',
                AllowedFilter::exact('exact__is_default', 'is_default'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
                'user.email',
                AllowedFilter::exact('exact__user.email', 'user.email'),
                'activeBy.username',
                AllowedFilter::exact('exact__activeBy.username', 'activeBy.username'),
                'activeBy.email',
                AllowedFilter::exact('exact__activeBy.email', 'activeBy.email'),
                AllowedFilter::scope('by_role', 'getFooterByRole')
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return FooterTemplate::class;
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
