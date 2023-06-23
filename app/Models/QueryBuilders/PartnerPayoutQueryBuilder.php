<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\PartnerPayout;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\UserCreditHistory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class PartnerPayoutQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return PartnerPayout::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new PartnerPayout())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'by_user_uuid',
                'partner_uuid',
                'amount',
                'time',
                'status',
                'payout_information_uuid'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'by_user_uuid',
                'partner_uuid',
                'amount',
                'time',
                'status',
                'payout_information_uuid'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'by_user_uuid',
                AllowedFilter::exact('exact__by_user_uuid', 'by_user_uuid'),
                'partner_uuid',
                AllowedFilter::exact('exact__partner_uuid', 'partner_uuid'),
                'partner.partner_email',
                AllowedFilter::exact('exact__partner.partner_email', 'partner.partner_email'),
                'amount',
                AllowedFilter::exact('exact__amount', 'amount'),
                'time',
                AllowedFilter::exact('exact__time', 'time'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'payout_information_uuid',
                AllowedFilter::exact('exact__payout_information_uuid', 'payout_information_uuid'),
                'created_at',
                AllowedFilter::exact('exact__created_at', 'created_at'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return PartnerPayout::class;
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
