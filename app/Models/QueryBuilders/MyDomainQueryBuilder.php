<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Domain;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyDomainQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Domain::where([
                    ['owner_uuid', auth()->userId()],
                    ['app_id', auth()->appId()]
                ]);
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Domain())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'verified_at',
                'business_uuid',
                'owner_uuid',
                'active_mailbox',
                'active_mailbox_status',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'verified_at',
                'business_uuid',
                'owner_uuid',
                'active_mailbox',
                'active_mailbox_status',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'verified_at',
                AllowedFilter::exact('exact__verified_at', 'verified_at'),
                'business_uuid',
                AllowedFilter::exact('exact__business_uuid', 'business_uuid'),
                'owner_uuid',
                AllowedFilter::exact('exact__owner_uuid', 'owner_uuid'),
                'active_mailbox',
                AllowedFilter::exact('exact__active_mailbox', 'active_mailbox'),
                'active_mailbox_status',
                AllowedFilter::exact('exact__active_mailbox_status', 'active_mailbox_status'),
                AllowedFilter::callback("domain_unique", function (Builder $query, $value) {
                    if ($value === 'domain_unique') {
                        $query->whereDoesntHave('websites', function (Builder $query) {
                            $query->whereNotNull('domain_uuid')
                                ->whereColumn('user_uuid', 'domains.owner_uuid');
                        });
                    }
                }),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Domain::class;
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
