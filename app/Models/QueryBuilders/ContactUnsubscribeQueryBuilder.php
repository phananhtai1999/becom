<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Company;
use App\Models\ContactUnsubscribe;
use App\Models\Country;
use App\Models\Remind;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class ContactUnsubscribeQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return ContactUnsubscribe::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new ContactUnsubscribe())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'contact_uuid',
                'unsubscribe'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'contact_uuid',
                'unsubscribe'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'contact_uuid',
                AllowedFilter::exact('exact__contact_uuid', 'contact_uuid'),
                'unsubscribe',
                AllowedFilter::exact('exact__unsubscribe', 'unsubscribe'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return ContactUnsubscribe::class;
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
