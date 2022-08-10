<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\WebsiteVerification;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class WebsiteVerificationQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return WebsiteVerification::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new WebsiteVerification())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'website_uuid',
                'token',
                'verified_at'

            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'website_uuid',
                'token',
                'verified_at'

            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'website_uuid',
                AllowedFilter::exact('exact__website_uuid', 'website_uuid'),
                'token',
                AllowedFilter::exact('exact__token', 'token'),
                'verified_at',
                AllowedFilter::exact('exact__verified_at', 'verified_at'),
            ]);
    }
}
