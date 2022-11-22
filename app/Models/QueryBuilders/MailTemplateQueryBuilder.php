<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\MailTemplate;
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
                'website_uuid',
                'user_uuid',
                'design',
                'publish_status',
                'type'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'subject',
                'body',
                'website_uuid',
                'user_uuid',
                'design',
                'publish_status',
                'type'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'subject',
                AllowedFilter::exact('exact__subject', 'subject'),
                'body',
                AllowedFilter::exact('exact__body', 'body'),
                'website_uuid',
                AllowedFilter::exact('exact__website_uuid', 'website_uuid'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'design',
                AllowedFilter::exact('exact__design', 'design'),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'publish_status',
                AllowedFilter::exact('exact__publish_status', 'publish_status'),
                'website.domain',
                AllowedFilter::exact('exact__website.domain', 'website.domain'),
                'user.username',
                AllowedFilter::exact('exact__user.username', 'user.username'),
            ]);
    }
}
