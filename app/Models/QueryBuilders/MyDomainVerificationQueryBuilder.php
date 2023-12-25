<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\DomainVerification;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyDomainVerificationQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return DomainVerification::select('domain_verifications.*')
            ->join('domains', 'domains.uuid', '=', 'domain_verifications.domain_uuid')
            ->where(
                ['domains.owner_uuid', auth()->userId()],
                ['domains.app_id', auth()->appId()]
            );
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new DomainVerification())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'domain_uuid',
                'token',
                'verified_at'

            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'domain_uuid',
                'token',
                'verified_at'

            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'domain_uuid',
                AllowedFilter::exact('exact__domain_uuid', 'domain_uuid'),
                'token',
                AllowedFilter::exact('exact__token', 'token'),
                'verified_at',
                AllowedFilter::exact('exact__verified_at', 'verified_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return DomainVerification::class;
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
