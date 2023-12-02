<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Role;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\WebsitePageShortCode;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class WebsitePageShortCodeQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return WebsitePageShortCode::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Role())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'status',
                'key',
                'parent_uuid',
                'name',
                'short_code',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'status',
                'key',
                'parent_uuid',
                'name',
                'short_code',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'key',
                AllowedFilter::exact('exact__key', 'key'),
                'parent_uuid',
                AllowedFilter::exact('exact__parent_uuid', 'parent_uuid'),
                'short_code',
                AllowedFilter::exact('exact__short_code', 'short_code'),
                AllowedFilter::scope('short_code_root'),

            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return WebsitePageShortCode::class;
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
