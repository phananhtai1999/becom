<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Email;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class EmailQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Email::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Email())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'email',
                'age',
                'first_name',
                'last_name',
                'country',
                'city',
                'job',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'email',
                'age',
                'first_name',
                'last_name',
                'country',
                'city',
                'job',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'email',
                AllowedFilter::exact('exact__email', 'email'),
                'age',
                AllowedFilter::exact('exact__age', 'age'),
                'first_name',
                AllowedFilter::exact('exact__first_name', 'first_name'),
                'last_name',
                AllowedFilter::exact('exact__last_name', 'last_name'),
                'country',
                AllowedFilter::exact('exact__country', 'country'),
                'city',
                AllowedFilter::exact('exact__city', 'city'),
                'job',
                AllowedFilter::exact('exact__job', 'job'),
                'websites.domain',
                AllowedFilter::exact('exact__websites.domain', 'websites.domain'),
            ]);
    }
}
