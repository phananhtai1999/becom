<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\PayoutInformation;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;

class PayoutInformationQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return PayoutInformation::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new PayoutInformation())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'type',
                'email',
                'account_number',
                'payout_fee',
                'first_name',
                'last_name',
                'address',
                'city',
                'country',
                'phone',
                'name_on_account',
                'user_uuid',
                'is_default',
                'swift_code',
                'bank_name',
                'bank_address',
                'currency'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'type',
                'email',
                'account_number',
                'payout_fee',
                'first_name',
                'last_name',
                'address',
                'city',
                'country',
                'phone',
                'name_on_account',
                'user_uuid',
                'is_default',
                'swift_code',
                'bank_name',
                'bank_address',
                'currency'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'account_number',
                AllowedFilter::exact('exact__account_number', 'account_number'),
                'payout_fee',
                AllowedFilter::exact('exact__payout_fee', 'payout_fee'),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'address',
                AllowedFilter::exact('exact__address', 'address'),
                'city',
                AllowedFilter::exact('exact__city', 'city'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'phone',
                AllowedFilter::exact('exact__phone', 'phone'),
                'name_on_account',
                AllowedFilter::exact('exact__name_on_account', 'name_on_account'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'is_default',
                AllowedFilter::exact('exact__is_default', 'is_default'),
                'swift_code',
                AllowedFilter::exact('exact__swift_code', 'swift_code'),
                'bank_name',
                AllowedFilter::exact('exact__bank_name', 'bank_name'),
                'bank_address',
                AllowedFilter::exact('exact__bank_address', 'bank_address'),
                'currency',
                AllowedFilter::exact('exact__currency', 'currency'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return PayoutInformation::class;
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
