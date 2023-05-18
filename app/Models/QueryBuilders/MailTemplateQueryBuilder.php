<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\MailTemplate;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MailTemplateQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return MailTemplate::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new MailTemplate())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'subject',
                'body',
                'send_project_uuid',
                'business_category_uuid',
                'purpose_uuid',
                'user_uuid',
                'design',
                'publish_status',
                'type',
                'image'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'subject',
                'body',
                'send_project_uuid',
                'business_category_uuid',
                'purpose_uuid',
                'user_uuid',
                'design',
                'publish_status',
                'type',
                'image'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'subject',
                AllowedFilter::exact('exact__subject', 'subject'),
                'body',
                AllowedFilter::exact('exact__body', 'body'),
                'send_project_uuid',
                AllowedFilter::exact('exact__send_project_uuid', 'send_project_uuid'),
                'business_category_uuid',
                AllowedFilter::exact('exact__business_category_uuid', 'business_category_uuid'),
                'businessCategory.title',
                AllowedFilter::scope('exact__businessCategory.title', 'businessCategoryTitle'),
                'purpose_uuid',
                AllowedFilter::exact('exact__purpose_uuid', 'purpose_uuid'),
                'purpose.title',
                AllowedFilter::scope('exact__purpose.title', 'purposeTitle'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'design',
                AllowedFilter::exact('exact__design', 'design'),
                'image',
                AllowedFilter::exact('exact__image', 'image'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'sendProject.domain',
                AllowedFilter::exact('exact__sendProject.domain', 'sendProject.domain'),
                'sendProject.name',
                AllowedFilter::exact('exact__sendProject.name', 'sendProject.name'),
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
        return MailTemplate::class;
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
