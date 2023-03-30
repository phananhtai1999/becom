<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Company;
use App\Models\Country;
use App\Models\Remind;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class CountryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Country::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Country())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'national_flag',
                'country_code',
                'name',
                'country_phone_code',
                'sms_price',
                'email_price',
                'telegram_price',
                'viber_price',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'national_flag',
                'country_code',
                'name',
                'country_phone_code',
                'sms_price',
                'email_price',
                'telegram_price',
                'viber_price',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'country_code',
                AllowedFilter::exact('exact__country_code', 'country_code'),
                'country_phone_code',
                AllowedFilter::exact('exact__country_phone_code', 'country_phone_code'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Country::class;
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
