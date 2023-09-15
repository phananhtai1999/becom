<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\Language;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use App\Models\WebsitePage;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;
use Spatie\QueryBuilder\QueryBuilder;

class LanguageQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return Language::class;
    }

    /**
     * @return LanguageQueryBuilder|QueryBuilder
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function initialQuery()
    {
        $modelKeyName = (new Language())->getKeyName();
        //Exclude value
        $select = array_diff(array_merge(['created_at', 'updated_at'], (new Language())->getFillable()), request()->get('exclude', []));

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'fe',
                'status',
                'flag_image',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'fe',
                'status',
                'flag_image',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'fe',
                AllowedFilter::exact('exact__fe', 'fe'),
                'flag_image',
                AllowedFilter::exact('exact__flag_image', 'flag_image'),
                'status',
                AllowedFilter::exact('exact__status', 'status'),
            ])->select($select);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return Language::class;
    }

    /**
     * @param $search
     * @param $searchBy
     * @return mixed
     * @throws \Psr\Container\ContainerExceptionInterface
     * @throws \Psr\Container\NotFoundExceptionInterface
     */
    public static function searchQuery($search, $searchBy)
    {
        $initialQuery = static::initialQuery();
        $baseQuery = static::fillAble();

        return SearchQueryBuilder::search($baseQuery, $initialQuery, $search, $searchBy);
    }
}
