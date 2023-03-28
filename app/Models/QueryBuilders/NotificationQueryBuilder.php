<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Notification;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class NotificationQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Notification::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Notification())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'type',
                'type_uuid',
                'content',
                'user_uuid',
                'read'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'type',
                'type_uuid',
                'content',
                'user_uuid',
                'read'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'type',
                AllowedFilter::exact('exact__type', 'type'),
                'type_uuid',
                AllowedFilter::exact('exact__type_uuid', 'type_uuid'),
                'content',
                AllowedFilter::exact('exact__content', 'content'),
                'user_uuid',
                AllowedFilter::exact('exact__user_uuid', 'user_uuid'),
                'read',
                AllowedFilter::exact('exact__read', 'read'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Notification::class;
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
