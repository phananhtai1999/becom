<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
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
                'send_project_uuid',
                'token',
                'verified_at'

            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'send_project_uuid',
                'token',
                'verified_at'

            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'send_project_uuid',
                AllowedFilter::exact('exact__send_project_uuid', 'send_project_uuid'),
                'token',
                AllowedFilter::exact('exact__token', 'token'),
                'verified_at',
                AllowedFilter::exact('exact__verified_at', 'verified_at'),
                AllowedFilter::scope('from__verified_at'),
                AllowedFilter::scope('to__verified_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return WebsiteVerification::class;
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
