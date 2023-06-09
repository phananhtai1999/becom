<?php

namespace App\Models\QueryBuilders;

use App\Abstracts\AbstractQueryBuilder;
use App\Models\AssetGroup;
use App\Models\AssetSize;
use App\Models\SearchQueryBuilders\SearchQueryBuilder;
use Doctrine\DBAL\Query\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\Concerns\SortsQuery;

class AssetSizeQueryBuilder extends AbstractQueryBuilder
{
    /**
     * @return string
     */
    public static function baseQuery()
    {
        return AssetSize::class;
    }

    /**
     * @return SortsQuery|QueryBuilder
     */
    public static function initialQuery()
    {
        $modelKeyName = (new AssetGroup())->getKeyName();

        return static::for(static::baseQuery())
            ->allowedFields([
                $modelKeyName,
                'name',
                'width',
                'height',
                'asset_group_code',
            ])
            ->defaultSort('-created_at')
            ->allowedSorts([
                $modelKeyName,
                'name',
                'width',
                'height',
                'asset_group_uuid',
            ])
            ->allowedFilters([
                $modelKeyName,
                AllowedFilter::exact('exact__' . $modelKeyName, $modelKeyName),
                'width',
                AllowedFilter::exact('exact__width', 'width'),
                'name',
                AllowedFilter::exact('exact__name', 'name'),
                'height',
                AllowedFilter::exact('exact__height', 'height'),
                'asset_group_uuid',
                AllowedFilter::exact('exact__asset_group_uuid', 'asset_group_uuid'),
                'assetGroup.name',
                AllowedFilter::exact('exact__asset_group.name', 'assetGroup.name'),
            ]);
    }

    /**
     * @return string
     */
    public static function fillAble()
    {
        return AssetSize::class;
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
