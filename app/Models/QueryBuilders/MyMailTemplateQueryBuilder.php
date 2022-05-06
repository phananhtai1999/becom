<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\MailTemplate;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyMailTemplateQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return MailTemplate::select('mail_templates.*')
            ->join('websites', 'websites.uuid', '=', 'mail_templates.website_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey());
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
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'subject',
                'body',
                'website_uuid',
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
            ]);
    }
}
