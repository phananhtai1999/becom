<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Email;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class MyEmailQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Email::select('emails.*')
            ->join('websites', 'websites.uuid', '=', 'emails.website_uuid')
            ->where('websites.user_uuid', auth()->user()->getKey());
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
                'state',
                'job',
                'website_uuid',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'email',
                'age',
                'first_name',
                'last_name',
                'country',
                'state',
                'job',
                'website_uuid',
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
                'state',
                AllowedFilter::exact('exact__state', 'state'),
                'job',
                AllowedFilter::exact('exact__job', 'job'),
                'website_uuid',
                AllowedFilter::exact('exact__website_uuid', 'website_uuid'),
            ]);
    }
}
