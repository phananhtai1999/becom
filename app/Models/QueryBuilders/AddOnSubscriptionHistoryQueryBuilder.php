<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\AddOnSubscriptionHistory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class AddOnSubscriptionHistoryQueryBuilder extends AbstractQueryBuilder
{
    public static function baseQuery()
    {
        return AddOnSubscriptionHistory::class;
    }

    /**
     * @return AddOnSubscriptionHistoryQueryBuilder|\Spatie\QueryBuilder\QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new AddOnSubscriptionHistory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'description',
                'thumbnail',
                'status',
                'price',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'description',
                'thumbnail',
                'status',
                'price'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'description',
                AllowedFilter::exact('exact__description', 'description'),
                'thumbnail',
                AllowedFilter::exact('exact__thumbnail', 'thumbnail'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
                'price',
                AllowedFilter::exact('exact__price', 'price'),
            ]);

    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return AddOnSubscriptionHistory::class;
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
