<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\PartnerLevel;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\Status;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class PartnerLevelQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return PartnerLevel::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new PartnerLevel())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'title',
                'number_of_references',
                'commission',
                'content',
                'image'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'title',
                'number_of_references',
                'commission',
                'content',
                'image'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'number_of_references',
                AllowedFilter::exact('exact__number_of_references', 'number_of_references'),
                'commission',
                AllowedFilter::exact('exact__commission', 'commission'),
                'content',
                AllowedFilter::exact('exact__content', 'content'),
                'image',
                AllowedFilter::exact('exact__image', 'image'),
                AllowedFilter::scope('title'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return PartnerLevel::class;
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
