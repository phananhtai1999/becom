<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\BankInformation;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;

class BankInformationQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return BankInformation::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new BankInformation())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'swift_code',
                'bank_name',
                'bank_address',
                'is_verified',
                'currency'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'swift_code',
                'bank_name',
                'bank_address',
                'is_verified',
                'currency'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'swift_code',
                AllowedFilter::exact('exact__swift_code', 'swift_code'),
                'bank_name',
                AllowedFilter::exact('exact__bank_name', 'bank_name'),
                'bank_address',
                AllowedFilter::exact('exact__bank_address', 'bank_address'),
                'currency',
                AllowedFilter::exact('exact__currency', 'currency'),
                'is_verified',
                AllowedFilter::exact('exact__is_verified', 'is_verified'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return BankInformation::class;
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
