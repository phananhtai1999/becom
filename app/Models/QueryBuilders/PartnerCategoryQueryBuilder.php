<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\PartnerCategory;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\SectionCategory;
use App\Models\WebsitePage;
use App\Models\WebsitePageCategory;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class PartnerCategoryQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return PartnerCategory::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new PartnerCategory())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'title',
                'content',
                'image'
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'title',
                'content',
                'image'
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                AllowedFilter::scope('title'),
                'content',
                AllowedFilter::exact('exact__content', 'content'),
                'image',
                AllowedFilter::exact('exact__image', 'image'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return PartnerCategory::class;
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
